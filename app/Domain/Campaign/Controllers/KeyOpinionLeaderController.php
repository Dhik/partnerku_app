<?php

namespace App\Domain\Campaign\Controllers;

use App\Domain\Campaign\Enums\KeyOpinionLeaderEnum;
use App\Domain\Campaign\Enums\CampaignContentEnum;
use App\Domain\Campaign\Exports\KeyOpinionLeaderExport;
use App\Domain\Campaign\Models\KeyOpinionLeader;
use App\Domain\Campaign\Models\Statistic;
use App\Domain\Campaign\Requests\KeyOpinionLeaderRequest;
use App\Domain\Campaign\Requests\KolExcelRequest;
use App\Domain\User\Models\User;
use App\Domain\Niche\Models\Niche;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Utilities\Request;

class KeyOpinionLeaderController extends Controller
{

    public function __construct()
    {
    }

    /**
     * Get common data
     */
    protected function getCommonData(): array
    {
        $channels = KeyOpinionLeaderEnum::Channel ?? ['tiktok_video', 'instagram_feed', 'youtube_video'];
        $niches = Niche::orderBy('name')->get();
        $skinTypes = KeyOpinionLeaderEnum::SkinType ?? ['normal', 'oily', 'dry', 'combination'];
        $skinConcerns = KeyOpinionLeaderEnum::SkinConcern ?? ['acne', 'aging', 'dullness', 'sensitivity'];
        $contentTypes = KeyOpinionLeaderEnum::ContentType ?? ['review', 'tutorial', 'unboxing', 'lifestyle'];
        
        // Filter users by current tenant ID
        $marketingUsers = User::where('current_tenant_id', auth()->user()->current_tenant_id)
            ->get();

        return compact('channels', 'niches', 'skinTypes', 'skinConcerns', 'contentTypes', 'marketingUsers');
    }
    /**
     * Get KOL datatable query
     */
    private function getKOLDataTableQuery(Request $request)
{
    $query = KeyOpinionLeader::with(['picContact'])
        ->where('tenant_id', Auth::user()->current_tenant_id);

    // Apply filters
    if ($request->filled('niche')) {
        $query->where('niche', $request->niche);
    }

    if ($request->filled('status_recommendation')) {
        $query->where('status_recommendation', $request->status_recommendation);
    }

    if ($request->filled('approve_status')) {
        $approveValue = $request->approve_status;
        if ($approveValue === 'approved') {
            $query->where('approve', 1);
        } elseif ($approveValue === 'declined') {
            $query->where('approve', 0);
        } elseif ($approveValue === 'pending') {
            $query->whereNull('approve');
        }
    }

    if ($request->filled('tier')) {
        $tier = $request->tier;
        switch ($tier) {
            case 'Nano':
                $query->whereBetween('followers', [1000, 9999]);
                break;
            case 'Micro':
                $query->whereBetween('followers', [10000, 49999]);
                break;
            case 'Mid-Tier':
                $query->whereBetween('followers', [50000, 249999]);
                break;
            case 'Macro':
                $query->whereBetween('followers', [250000, 999999]);
                break;
            case 'Mega':
                $query->where('followers', '>=', 1000000);
                break;
            case 'Unknown':
                $query->where(function($q) {
                    $q->where('followers', '<', 1000)
                      ->orWhereNull('followers');
                });
                break;
        }
    }

    return $query;
}


    /**
     * Calculate CPM and status recommendation
     */
    protected function calculateCpmAndStatus(KeyOpinionLeader $kol): array
    {
        $avgViews = $kol->average_view ?: 0;
        $rate = $kol->rate ?: 0;
        
        // CPM calculation: (harga/slot / avg(views dari 10 video)) * 1000
        $cpm = $avgViews > 0 ? ($rate / $avgViews) * 1000 : 0;
        
        // Status rekomendasi: CPM < 25000 -> Worth it, CPM >= 25000 -> Gagal
        $statusRecommendation = $cpm < 25000 ? 'Worth it' : 'Gagal';
        
        return [
            'cpm' => round($cpm, 2),
            'status_recommendation' => $statusRecommendation
        ];
    }

    /**
     * @throws Exception
     */
    public function updateApprovalStatus(Request $request, KeyOpinionLeader $keyOpinionLeader)
{
    $this->authorize('updateKOL', KeyOpinionLeader::class);
    
    $request->validate([
        'approve' => 'required|boolean'
    ]);
    
    try {
        $keyOpinionLeader->approve = $request->approve;
        $keyOpinionLeader->save();
        
        $status = $request->approve ? 'approved' : 'declined';
        
        return response()->json([
            'success' => true,
            'message' => "KOL @{$keyOpinionLeader->username} has been {$status} successfully",
            'status' => $status,
            'approve' => $keyOpinionLeader->approve
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update approval status: ' . $e->getMessage()
        ], 500);
    }
}
// 3. Update your get() method to include the approval status column
public function get(Request $request): JsonResponse
{
    $this->authorize('viewKOL', KeyOpinionLeader::class);

    $query = $this->getKOLDataTableQuery($request);

    return DataTables::of($query)
        ->addColumn('pic_contact_name', function ($row) {
            return $row->picContact->name ?? 'empty';
        })
        ->addColumn('approval_status', function ($row) {
            if ($row->approve === 1) {
                return '<span class="badge badge-success">
                            <i class="fas fa-check"></i> Approved
                        </span>';
            } elseif ($row->approve === 0) {
                return '<span class="badge badge-danger">
                            <i class="fas fa-times"></i> Declined
                        </span>';
            } else {
                return '<span class="badge badge-warning">
                            <i class="fas fa-clock"></i> Pending
                        </span>';
            }
        })
        ->addColumn('approval_actions', function ($row) {
            $user = Auth::user();
            
            if (!$user->can('updateKOL', KeyOpinionLeader::class)) {
                return '<span class="text-muted">No permission</span>';
            }
            
            $actions = '';
            
            if ($row->approve !== 1) {
                $actions .= '<button onclick="updateApprovalStatus(' . $row->id . ', true)" 
                                class="btn btn-success btn-xs mr-1" 
                                title="Approve KOL">
                                <i class="fas fa-check"></i> Approve
                            </button>';
            }
            
            if ($row->approve !== 0) {
                $actions .= '<button onclick="updateApprovalStatus(' . $row->id . ', false)" 
                                class="btn btn-danger btn-xs" 
                                title="Decline KOL">
                                <i class="fas fa-times"></i> Decline
                            </button>';
            }
            
            return $actions ?: '<span class="text-muted">-</span>';
        })
        ->addColumn('tier_display', function ($row) {
            $followers = $row->followers ?: 0;
            
            if ($followers >= 1000 && $followers < 10000) {
                $tier = 'Nano';
                $badgeClass = 'badge-info';
            } elseif ($followers >= 10000 && $followers < 50000) {
                $tier = 'Micro';
                $badgeClass = 'badge-purple';
            } elseif ($followers >= 50000 && $followers < 250000) {
                $tier = 'Mid-Tier';
                $badgeClass = 'badge-warning';
            } elseif ($followers >= 250000 && $followers < 1000000) {
                $tier = 'Macro';
                $badgeClass = 'badge-success';
            } elseif ($followers >= 1000000) {
                $tier = 'Mega';
                $badgeClass = 'badge-danger';
            } else {
                $tier = 'Unknown';
                $badgeClass = 'badge-secondary';
            }
            
            return '<span class="badge ' . $badgeClass . '">' . $tier . '</span>';
        })
        ->addColumn('actions', function ($row) {
            $user = Auth::user();
            $actions = '';
            
            // WhatsApp button
            if (!empty($row->phone_number)) {
                $phoneNumber = preg_replace('/[^0-9]/', '', $row->phone_number);
                if (substr($phoneNumber, 0, 1) === '0') {
                    $phoneNumber = '62' . substr($phoneNumber, 1);
                }
                $waLink = 'https://wa.me/' . $phoneNumber;
                
                $actions .= '<a href="' . $waLink . '" class="btn btn-success btn-xs" target="_blank" title="WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </a> ';
            }
            
            $actions .= '<a href="' . route('kol.show', $row->id) . '" class="btn btn-info btn-xs" title="View">
                            <i class="fas fa-eye"></i>
                        </a> ';
            
            if ($user->hasAnyRole(['superadmin', 'client_1', 'tim_ads']) && $user->can('updateKOL', KeyOpinionLeader::class)) {
                $actions .= '<button onclick="openEditModal(' . $row->id . ')" class="btn btn-primary btn-xs" title="Edit">
                                <i class="fas fa-pencil-alt"></i>
                            </button> ';
            }
            
            if ($user->hasAnyRole(['superadmin', 'client_1', 'tim_ads']) && $user->can('deleteKOL', KeyOpinionLeader::class)) {
                $actions .= '<button onclick="deleteKol(' . $row->id . ')" class="btn btn-danger btn-xs" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>';
            }
            
            return $actions;
        })
        ->addColumn('refresh_follower', function ($row) {
            return '<button class="btn btn-info btn-xs refresh-follower" data-id="' . $row->username . '">
                        <i class="fas fa-sync-alt"></i>
                    </button>';
        })
        ->addColumn('cpm_display', function ($row) {
            return $row->cpm ? number_format($row->cpm, 0, ',', '.') : '-';
        })
        ->addColumn('status_recommendation_display', function ($row) {
            if (!$row->status_recommendation) {
                return '<span class="badge badge-secondary">-</span>';
            }
            $badgeClass = $row->status_recommendation === 'Worth it' ? 'badge-success' : 'badge-danger';
            return '<span class="badge ' . $badgeClass . '">' . $row->status_recommendation . '</span>';
        })
        ->editColumn('rate', function ($row) {
            return number_format($row->rate, 0, ',', '.');
        })
        ->editColumn('followers', function ($row) {
            return number_format($row->followers, 0, ',', '.');
        })
        ->rawColumns([
            'actions', 
            'refresh_follower', 
            'cpm_display',
            'status_recommendation_display',
            'tier_display',
            'approval_status',
            'approval_actions'
        ])
        ->toJson();
}
    public function getKpiData(Request $request)
{
    $query = KeyOpinionLeader::where('tenant_id', Auth::user()->current_tenant_id);

    // Apply the same filters as getKOLDataTableQuery
    if ($request->filled('niche')) {
        $query->where('niche', $request->niche);
    }

    if ($request->filled('status_recommendation')) {
        $query->where('status_recommendation', $request->status_recommendation);
    }

    if ($request->filled('approve_status')) {
        $approveValue = $request->approve_status;
        if ($approveValue === 'approved') {
            $query->where('approve', 1);
        } elseif ($approveValue === 'declined') {
            $query->where('approve', 0);
        } elseif ($approveValue === 'pending') {
            $query->whereNull('approve');
        }
    }

    if ($request->filled('tier')) {
        $tier = $request->tier;
        switch ($tier) {
            case 'Nano':
                $query->whereBetween('followers', [1000, 9999]);
                break;
            case 'Micro':
                $query->whereBetween('followers', [10000, 49999]);
                break;
            case 'Mid-Tier':
                $query->whereBetween('followers', [50000, 249999]);
                break;
            case 'Macro':
                $query->whereBetween('followers', [250000, 999999]);
                break;
            case 'Mega':
                $query->where('followers', '>=', 1000000);
                break;
            case 'Unknown':
                $query->where(function($q) {
                    $q->where('followers', '<', 1000)
                      ->orWhereNull('followers');
                });
                break;
        }
    }

    $totalKol = $query->count();
    $worthItCount = (clone $query)->where('status_recommendation', 'Worth it')->count();
    $avgCpm = (clone $query)->whereNotNull('cpm')->avg('cpm');
    $worthItPercentage = $totalKol > 0 ? round(($worthItCount / $totalKol) * 100, 1) : 0;

    return response()->json([
        'total_kol' => $totalKol,
        'worth_it_count' => $worthItCount,
        'avg_cpm' => round($avgCpm ?? 0),
        'worth_it_percentage' => $worthItPercentage
    ]);
}

    /**
     * Fetch video statistics and update average views for a KOL (Test Route)
     */
    public function fetchVideoStatistics(Request $request): JsonResponse
    {
        try {   
            $username = $request->get('username');
            $tenantId = $request->get('tenant_id');
            
            $kol = KeyOpinionLeader::where('username', $username)
                                ->where('tenant_id', $tenantId)
                                ->first();

            if (!$kol) {
                return response()->json([
                    'success' => false,
                    'message' => "KOL not found with username: {$username} and tenant_id: {$tenantId}"
                ], 404);
            }

            // Check if KOL has video links
            if (empty($kol->video_10_links)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No video links found for this KOL',
                    'kol_info' => [
                        'id' => $kol->id,
                        'username' => $kol->username,
                        'current_average_view' => $kol->average_view,
                        'price_per_slot' => $kol->price_per_slot
                    ]
                ], 400);
            }

            // Decode video links
            $videoLinks = json_decode($kol->video_10_links, true);
            
            if (empty($videoLinks) || !is_array($videoLinks)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid video links data',
                    'video_10_links' => $kol->video_10_links
                ], 400);
            }

            $viewCounts = [];
            $videoDetails = [];
            $failedLinks = [];

            // Process each video link
            foreach ($videoLinks as $index => $link) {
                if (empty(trim($link))) {
                    continue;
                }

                try {
                    // Initialize TikTok scrapper service
                    $tiktokService = app(\App\Domain\Campaign\Service\TiktokScrapperService::class);
                    
                    // Fetch data from TikTok API
                    $videoData = $tiktokService->getData($link);
                    
                    if ($videoData && isset($videoData['view'])) {
                        $viewCounts[] = (int) $videoData['view'];
                        $videoDetails[] = [
                            'index' => $index + 1,
                            'link' => $link,
                            'views' => $videoData['view'],
                            'likes' => $videoData['like'] ?? 0,
                            'comments' => $videoData['comment'] ?? 0,
                            'shares' => $videoData['share'] ?? 0,
                        ];
                    } else {
                        $failedLinks[] = [
                            'index' => $index + 1,
                            'link' => $link,
                            'reason' => 'No data returned from API'
                        ];
                    }
                    
                    // Add small delay to avoid rate limiting
                    sleep(1);
                    
                } catch (\Exception $e) {
                    $failedLinks[] = [
                        'index' => $index + 1,
                        'link' => $link,
                        'reason' => $e->getMessage()
                    ];
                }
            }

            // Calculate results
            $oldAverageViews = $kol->average_view;
            $newAverageViews = !empty($viewCounts) ? round(array_sum($viewCounts) / count($viewCounts)) : 0;
            
            // Update KOL if we have valid data
            $updated = false;
            $newCpm = null;
            $newStatus = null;
            
            if ($newAverageViews > 0) {
                $kol->update(['average_view' => $newAverageViews]);
                $updated = true;
                
                // Calculate CPM using price_per_slot
                // Formula: cpm = (price_per_slot / average_view) * 1000
                if ($kol->price_per_slot && $newAverageViews > 0) {
                    $newCpm = round(($kol->price_per_slot / $newAverageViews) * 1000);
                    
                    // Set status_recommendation based on CPM
                    // if cpm < 25000 then "Worth it" else "Gagal"
                    $newStatus = $newCpm < 25000 ? 'Worth it' : 'Gagal';
                    
                    $kol->update([
                        'cpm' => $newCpm,
                        'status_recommendation' => $newStatus
                    ]);
                } else {
                    // If price_per_slot is not set, CPM cannot be calculated
                    $newCpm = 0;
                    $newStatus = 'Cannot calculate (no price_per_slot)';
                    
                    $kol->update([
                        'cpm' => $newCpm,
                        'status_recommendation' => $newStatus
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => $updated ? 'Video statistics updated successfully' : 'No valid video data found',
                'kol_info' => [
                    'id' => $kol->id,
                    'username' => $kol->username,
                    'tenant_id' => $kol->tenant_id,
                    'channel' => $kol->channel
                ],
                'statistics' => [
                    'total_video_links' => count($videoLinks),
                    'successful_videos' => count($viewCounts),
                    'failed_videos' => count($failedLinks),
                    'old_average_views' => $oldAverageViews,
                    'new_average_views' => $newAverageViews,
                    'updated' => $updated
                ],
                'cpm_calculation' => [
                    'price_per_slot' => $kol->price_per_slot,
                    'new_average_views' => $newAverageViews,
                    'formula' => 'cpm = (price_per_slot / average_view) * 1000',
                    'calculation' => $kol->price_per_slot && $newAverageViews > 0 
                        ? "({$kol->price_per_slot} / {$newAverageViews}) * 1000 = {$newCpm}"
                        : 'Cannot calculate - missing price_per_slot or zero average_view',
                    'new_cpm' => $newCpm,
                    'new_status' => $newStatus,
                    'status_rule' => 'Worth it if CPM < 25,000, else Gagal'
                ],
                'view_counts' => $viewCounts,
                'video_details' => $videoDetails,
                'failed_links' => $failedLinks
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Select KOL by username
     */
    public function select(Request $request): JsonResponse
    {
        $this->authorize('viewKOL', KeyOpinionLeader::class);

        $search = $request->input('search', '');
        $user = Auth::user();
        
        $query = KeyOpinionLeader::select(['id', 'username', 'name', 'channel'])
            ->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });

        // Apply tenant filter for clients
        if ($user->hasRole(['client_1', 'client_2'])) {
            $query->where('tenant_id', $user->current_tenant_id);
        }

        $kols = $query->limit(10)->get();

        return response()->json($kols);
    }

    /**
     * Show list KOL
     */
    public function index(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $this->authorize('viewKOL', KeyOpinionLeader::class);
        return view('admin.kol.index', $this->getCommonData());
    }

    
    /**
     * Show Worth It KOLs
     */
    public function worthItIndex(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $this->authorize('viewKOL', KeyOpinionLeader::class);
        return view('admin.kol.worth-it', $this->getCommonData());
    }

    /**
     * Get Worth It KOLs data for DataTable
     */
    public function getWorthItKols(Request $request): JsonResponse
    {
        $this->authorize('viewKOL', KeyOpinionLeader::class);

        $user = Auth::user();
        $query = KeyOpinionLeader::with(['picContact', 'createdBy'])
            ->select('key_opinion_leaders.*')
            ->where('tenant_id', $user->current_tenant_id);

        // Apply search filters
        if ($request->filled('channel')) {
            $query->where('channel', $request->input('channel'));
        }

        if ($request->filled('niche')) {
            $query->where('niche', $request->input('niche'));
        }

        if ($request->filled('content_type')) {
            $query->where('content_type', $request->input('content_type'));
        }

        if ($request->filled('pic_contact')) {
            $query->where('pic_contact', $request->input('pic_contact'));
        }

        if ($request->filled('followersMin')) {
            $query->where('followers', '>=', (int) $request->input('followersMin'));
        }

        if ($request->filled('followersMax')) {
            $query->where('followers', '<=', (int) $request->input('followersMax'));
        }

        // Filter only KOLs with calculated "Worth it" status
        $allKols = $query->get();
        $worthItKols = $allKols->filter(function ($kol) {
            $cpmData = $this->calculateCpmAndStatus($kol);
            return $cpmData['status_recommendation'] === 'Worth it';
        });

        return DataTables::of($worthItKols)
            ->addColumn('pic_contact_name', function ($row) {
                return $row->picContact->name ?? 'empty';
            })
            ->addColumn('actions', function ($row) {
                $user = Auth::user();
                $actions = '';
                
                // WhatsApp button
                if (!empty($row->phone_number)) {
                    $phoneNumber = preg_replace('/[^0-9]/', '', $row->phone_number);
                    if (substr($phoneNumber, 0, 1) === '0') {
                        $phoneNumber = '62' . substr($phoneNumber, 1);
                    }
                    $waLink = 'https://wa.me/' . $phoneNumber;
                    
                    $actions .= '<a href="' . $waLink . '" class="btn btn-success btn-xs" target="_blank" title="WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </a> ';
                }
                
                // View button - all roles can view
                $actions .= '<a href="' . route('kol.show', $row->id) . '" class="btn btn-info btn-xs" title="View">
                                <i class="fas fa-eye"></i>
                            </a> ';
                
                // Edit button - Admin, Client1, TimAds can edit (TimInternal cannot edit)
                if ($user->hasAnyRole(['superadmin', 'client_1', 'tim_ads']) && $user->can('updateKOL', KeyOpinionLeader::class)) {
                    $actions .= '<button onclick="openEditModal(' . $row->id . ')" class="btn btn-primary btn-xs" title="Edit">
                                    <i class="fas fa-pencil-alt"></i>
                                </button> ';
                }
                
                // Delete button - Admin, Client1, TimAds only (TimInternal cannot delete)
                if ($user->hasAnyRole(['superadmin', 'client_1', 'tim_ads']) && $user->can('deleteKOL', KeyOpinionLeader::class)) {
                    $actions .= '<button onclick="deleteKol(' . $row->id . ')" class="btn btn-danger btn-xs" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>';
                }
                
                return $actions;
            })
            ->addColumn('refresh_follower', function ($row) {
                return '<button class="btn btn-info btn-xs refresh-follower" data-id="' . $row->username . '">
                            <i class="fas fa-sync-alt"></i>
                        </button>';
            })
            ->addColumn('engagement_rate_display', function ($row) {
                return $row->engagement_rate ? number_format($row->engagement_rate, 2) . '%' : '-';
            })
            ->addColumn('cpm_display', function ($row) {
                $cpmData = $this->calculateCpmAndStatus($row);
                return number_format($cpmData['cpm'], 0, ',', '.');
            })
            ->addColumn('status_recommendation_display', function ($row) {
                $cpmData = $this->calculateCpmAndStatus($row);
                return '<span class="badge badge-success">Worth it</span>';
            })
            ->addColumn('tier_display', function ($row) {
                $followers = $row->followers ?: 0;
                
                if ($followers >= 1000 && $followers < 10000) {
                    $tier = 'Nano';
                    $badgeClass = 'badge-info';
                } elseif ($followers >= 10000 && $followers < 50000) {
                    $tier = 'Micro';
                    $badgeClass = 'badge-purple';
                } elseif ($followers >= 50000 && $followers < 250000) {
                    $tier = 'Mid-Tier';
                    $badgeClass = 'badge-warning';
                } elseif ($followers >= 250000 && $followers < 1000000) {
                    $tier = 'Macro';
                    $badgeClass = 'badge-success';
                } elseif ($followers >= 1000000) {
                    $tier = 'Mega';
                    $badgeClass = 'badge-danger';
                } else {
                    $tier = 'Unknown';
                    $badgeClass = 'badge-secondary';
                }
                
                return '<span class="badge ' . $badgeClass . '">' . $tier . '</span>';
            })
            ->editColumn('rate', function ($row) {
                return number_format($row->rate, 0, ',', '.');
            })
            ->editColumn('followers', function ($row) {
                return number_format($row->followers, 0, ',', '.');
            })
            ->rawColumns([
                'actions', 
                'refresh_follower', 
                'cpm_display',
                'status_recommendation_display',
                'tier_display'
            ])
            ->toJson();
    }

    /**
     * Create a new KOL
     */
    public function create(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $this->authorize('createKOL', KeyOpinionLeader::class);

        return view('admin.kol.create', $this->getCommonData());
    }

    /**
     * Create with excel form
     */
    public function createExcelForm(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $this->authorize('createKOL', KeyOpinionLeader::class);

        return view('admin.kol.create-excel', $this->getCommonData());
    }

    /**
     * store KOL
     */
    public function store(KeyOpinionLeaderRequest $request): RedirectResponse
    {
        $this->authorize('createKOL', KeyOpinionLeader::class);

        try {
            $data = $request->validated();
            $data['created_by'] = Auth::id();
            $data['tenant_id'] = Auth::user()->current_tenant_id;
            
            // Set fixed values as per requirements
            $data['average_view'] = 1; // Fixed value as per requirement
            $data['address'] = null;   // Set address as null as per requirement
            
            // Set price_per_slot same as rate
            if (isset($data['rate'])) {
                $data['price_per_slot'] = $data['rate'];
            }
            
            // Process video_10_links - filter out empty values
            if (isset($data['video_10_links']) && is_array($data['video_10_links'])) {
                $videoLinks = array_filter($data['video_10_links'], function($link) {
                    return !empty(trim($link));
                });
                
                // Re-index array to remove gaps and store as JSON
                $data['video_10_links'] = json_encode(array_values($videoLinks));
            } else {
                $data['video_10_links'] = json_encode([]);
            }
            
            // Calculate CPM and status - with fixed average_view = 1
            $avgViews = 1; // Fixed value as per requirement
            $rate = isset($data['rate']) ? (float) $data['rate'] : 0;
            
            $cpm = $rate > 0 ? ($rate / $avgViews) * 1000 : 0;
            $data['cpm'] = (int) round($cpm, 0); // Store as integer in database
            $data['status_recommendation'] = $cpm < 25000 ? 'Worth it' : 'Gagal';

            // Ensure numeric fields are properly cast
            $numericFields = ['rate', 'price_per_slot', 'gmv', 'average_view'];
            foreach ($numericFields as $field) {
                if (isset($data[$field])) {
                    $data[$field] = $data[$field] !== '' ? (int) $data[$field] : null;
                }
            }

            $kol = KeyOpinionLeader::create($data);
            
            $videoLinksCount = count(json_decode($data['video_10_links'], true));
            
            // Auto refresh statistics and followers after successful creation
            $this->refreshKolDataInBackground($kol->username, $kol->tenant_id);
            
            return redirect()
                ->route('kol.show', $kol->id)
                ->with([
                    'alert' => 'success',
                    'message' => trans('messages.success_save', ['model' => trans('labels.key_opinion_leader')]) . 
                            ' (' . $videoLinksCount . ' video links saved). Statistics and followers are being refreshed in the background.',
                ]);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error creating KOL:', $e->errors());
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
                
        } catch (\Exception $e) {
            Log::error('Error creating KOL: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create KOL: ' . $e->getMessage()]);
        }
    }

    /**
     * Refresh KOL statistics and followers in background
     */
    private function refreshKolDataInBackground(string $username, int $tenantId): void
    {
        try {
            // First refresh followers/following statistics
            $followersResponse = $this->refreshFollowersFollowingSingle($username);
            
            Log::info('Auto refresh followers completed for KOL: ' . $username, [
                'response' => $followersResponse->getData()
            ]);
            
            // Then refresh video statistics if there are video links
            $kol = KeyOpinionLeader::where('username', $username)
                                ->where('tenant_id', $tenantId)
                                ->first();
                                
            if ($kol && !empty($kol->video_10_links)) {
                $videoLinks = json_decode($kol->video_10_links, true);
                if (!empty($videoLinks) && is_array($videoLinks)) {
                    // Create request with proper query parameters using Yajra Request
                    $request = new \Yajra\DataTables\Utilities\Request();
                    $request->merge([
                        'username' => $username,
                        'tenant_id' => $tenantId
                    ]);
                    
                    $videoResponse = $this->fetchVideoStatistics($request);
                    
                    Log::info('Auto refresh video statistics completed for KOL: ' . $username, [
                        'response' => $videoResponse->getData()
                    ]);
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Error in background refresh for KOL: ' . $username, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * store KOL via excel
     */
    public function storeExcel(KolExcelRequest $request): JsonResponse
    {
        $this->authorize('createKOL', KeyOpinionLeader::class);

        try {
            $data = $request->input('data');
            $user = Auth::user();
            $successCount = 0;
            $errorCount = 0;

            foreach ($data as $row) {
                try {
                    $kolData = array_merge($row, [
                        'created_by' => $user->id,
                        'tenant_id' => $user->current_tenant_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // Calculate CPM and status
                    $avgViews = $kolData['average_view'] ?? 0;
                    $rate = $kolData['rate'] ?? 0;
                    $cpm = $avgViews > 0 ? ($rate / $avgViews) * 1000 : 0;
                    $kolData['cpm'] = round($cpm, 2);
                    $kolData['status_recommendation'] = $cpm < 25000 ? 'Worth it' : 'Gagal';

                    KeyOpinionLeader::create($kolData);
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error('Error creating KOL from excel: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$successCount} KOLs. {$errorCount} errors occurred.",
                'success_count' => $successCount,
                'error_count' => $errorCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error in storeExcel: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to import KOLs.'
            ], 500);
        }
    }

    /**
     * Edit a new KOL
     */
    public function edit(KeyOpinionLeader $keyOpinionLeader): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $this->authorize('updateKOL', KeyOpinionLeader::class);
        return view('admin.kol.edit', array_merge(['keyOpinionLeader' => $keyOpinionLeader], $this->getCommonData()));
    }

    public function getEditData(KeyOpinionLeader $keyOpinionLeader): JsonResponse
    {
        $this->authorize('updateKOL', KeyOpinionLeader::class);
        
        return response()->json($keyOpinionLeader->toArray());
    }

    /**
     * Update KOL
     */
    public function update(KeyOpinionLeader $keyOpinionLeader, KeyOpinionLeaderRequest $request): JsonResponse|RedirectResponse
    {
        $this->authorize('updateKOL', KeyOpinionLeader::class);
        
        try {
            $data = $request->validated();
            
            // Set price_per_slot same as rate (as per requirement)
            if (isset($data['rate'])) {
                $data['price_per_slot'] = $data['rate'];
            }
            
            // Set address as null (as per requirement)
            $data['address'] = null;
            
            // Process video_10_links - filter out empty values
            if (isset($data['video_10_links']) && is_array($data['video_10_links'])) {
                $videoLinks = array_filter($data['video_10_links'], function($link) {
                    return !empty(trim($link));
                });
                
                // Re-index array to remove gaps and store as JSON
                $data['video_10_links'] = json_encode(array_values($videoLinks));
            }
            
            // Calculate CPM and status - use existing average_view or keep current value
            $avgViews = $keyOpinionLeader->average_view ?? 1; // Use existing average_view from database
            $rate = isset($data['rate']) ? (float) $data['rate'] : ($keyOpinionLeader->rate ?? 0);
            
            $cpm = $avgViews > 0 ? ($rate / $avgViews) * 1000 : 0;
            $data['cpm'] = (int) round($cpm, 0); // Store as integer in database
            $data['status_recommendation'] = $cpm < 25000 ? 'Worth it' : 'Gagal';
            
            // Ensure numeric fields are properly cast
            $numericFields = ['rate', 'price_per_slot', 'gmv'];
            foreach ($numericFields as $field) {
                if (isset($data[$field])) {
                    $data[$field] = $data[$field] !== '' ? (int) $data[$field] : null;
                }
            }
            
            $keyOpinionLeader->update($data);
            
            if ($request->ajax()) {
                $videoLinksCount = isset($data['video_10_links']) ? count(json_decode($data['video_10_links'], true)) : 0;
                
                return response()->json([
                    'success' => true,
                    'message' => trans('messages.success_update', ['model' => trans('labels.key_opinion_leader')]) . 
                            ' (' . $videoLinksCount . ' video links saved)',
                    'data' => $keyOpinionLeader->fresh()
                ]);
            }
            
            return redirect()
                ->route('kol.show', $keyOpinionLeader->id)
                ->with([
                    'alert' => 'success',
                    'message' => trans('messages.success_update', ['model' => trans('labels.key_opinion_leader')]),
                ]);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error updating KOL:', $e->errors());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
                
        } catch (\Exception $e) {
            Log::error('Error updating KOL: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update KOL information: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update KOL information: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete KOL
     */
    public function destroy(KeyOpinionLeader $keyOpinionLeader): JsonResponse
    {
        $this->authorize('deleteKOL', KeyOpinionLeader::class);

        try {
            DB::beginTransaction();

            // Delete related statistics first (if they reference campaign_contents)
            $campaignContentIds = $keyOpinionLeader->campaignContents()->pluck('id');
            if ($campaignContentIds->isNotEmpty()) {
                Statistic::whereIn('campaign_content_id', $campaignContentIds)->delete();
            }

            // Delete related campaign contents
            $keyOpinionLeader->campaignContents()->delete();

            // Finally delete the KOL
            $keyOpinionLeader->delete();

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => trans('messages.success_delete', ['model' => trans('labels.key_opinion_leader')])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting KOL: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete KOL and related data.'
            ], 500);
        }
    }

    /**
     * show KOL
     */
    public function show(KeyOpinionLeader $keyOpinionLeader): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $this->authorize('viewKOL', KeyOpinionLeader::class);

        // Calculate tiering based on followers
        $followers = $keyOpinionLeader->followers ?: 0;
        
        if ($followers >= 1000 && $followers < 10000) {
            $tiering = "Nano";
            $er_top = 0.1;
            $er_bottom = 0.04;
            $cpm_target = 35000;
        } elseif ($followers >= 10000 && $followers < 50000) {
            $tiering = "Micro";
            $er_top = 0.05;
            $er_bottom = 0.02;
            $cpm_target = 35000;
        } elseif ($followers >= 50000 && $followers < 250000) {
            $tiering = "Mid-Tier";
            $er_top = 0.03;
            $er_bottom = 0.015;
            $cpm_target = 25000;
        } elseif ($followers >= 250000 && $followers < 1000000) {
            $tiering = "Macro TOFU";
            $er_top = 0.025;
            $er_bottom = 0.01;
            $cpm_target = 10000;
        } elseif ($followers >= 1000000 && $followers < 2000000) {
            $tiering = "Mega TOFU";
            $er_top = 0.02;
            $er_bottom = 0.01;
            $cpm_target = 10000;
        } elseif ($followers >= 2000000) {
            $tiering = "Mega MOFU";
            $er_top = 0.02;
            $er_bottom = 0.01;
            $cpm_target = 35000;
        } else {
            $tiering = "Unknown";
            $er_top = null;
            $er_bottom = null;
            $cpm_target = null;
        }

        // Get statistics for this KOL
        $statistics = Statistic::whereHas('campaignContent', function ($query) use ($keyOpinionLeader) {
            $query->where('username', $keyOpinionLeader->username);
        })->get();

        $total_views = $statistics->sum('view');
        $total_likes = $statistics->sum('like');
        $total_comments = $statistics->sum('comment');

        // Calculate CPM actual
        $cpm_actual = $total_views > 0
            ? ($keyOpinionLeader->rate / $total_views) * $keyOpinionLeader->followers
            : 0;

        // Calculate ER actual
        $er_actual = $total_views > 0
            ? (($total_likes + $total_comments) / $total_views) * 100
            : 0;

        // Calculate CPM and status recommendation
        $cpmData = $this->calculateCpmAndStatus($keyOpinionLeader);

        return view('admin.kol.show', compact(
            'keyOpinionLeader', 
            'tiering', 
            'er_top', 
            'er_bottom', 
            'cpm_target', 
            'cpm_actual', 
            'er_actual',
            'cpmData'
        ));
    }

    public function chart(): JsonResponse
    {
        $this->authorize('viewKOL', KeyOpinionLeader::class);
        
        $user = Auth::user();
        $query = KeyOpinionLeader::select('channel', DB::raw('count(*) as count'))
            ->groupBy('channel');

        // Apply tenant filter for clients
        if ($user->hasRole(['client_1', 'client_2'])) {
            $query->where('tenant_id', $user->current_tenant_id);
        }

        $data = $query->get();
        
        $response = [
            'labels' => $data->pluck('channel'),
            'values' => $data->pluck('count'),
        ];
        
        return response()->json($response);
    }

    public function averageRate(): JsonResponse
    {
        $this->authorize('viewKOL', KeyOpinionLeader::class);
        
        $user = Auth::user();
        $query = KeyOpinionLeader::select('channel', DB::raw('AVG(rate) as average_rate'))
            ->groupBy('channel');

        // Apply tenant filter for clients
        if ($user->hasRole(['client_1', 'client_2'])) {
            $query->where('tenant_id', $user->current_tenant_id);
        }

        $data = $query->get();

        $response = [
            'labels' => $data->pluck('channel'),
            'values' => $data->pluck('average_rate')
        ];

        return response()->json($response);
    }

    /**
     * show KOL Json
     */
    public function showJson(KeyOpinionLeader $keyOpinionLeader): JsonResponse
    {
        $this->authorize('viewKOL', KeyOpinionLeader::class);

        $cpmData = $this->calculateCpmAndStatus($keyOpinionLeader);
        $kolData = $keyOpinionLeader->toArray();
        $kolData['cpm_calculated'] = $cpmData['cpm'];
        $kolData['status_recommendation_calculated'] = $cpmData['status_recommendation'];

        return response()->json($kolData);
    }

    /**
     * Export KOL
     */
    public function export(Request $request): Response|BinaryFileResponse
    {
        $this->authorize('viewKOL', KeyOpinionLeader::class);

        $user = Auth::user();
        $query = KeyOpinionLeader::query();

        // Apply tenant filter for clients
        if ($user->hasRole(['client_1', 'client_2'])) {
            $query->where('tenant_id', $user->current_tenant_id);
        }

        // Apply filters
        if ($request->filled('channel')) {
            $query->where('channel', $request->input('channel'));
        }

        if ($request->filled('niche')) {
            $query->where('niche', $request->input('niche'));
        }

        if ($request->filled('content_type')) {
            $query->where('content_type', $request->input('content_type'));
        }

        if ($request->filled('pic_contact')) {
            $query->where('pic_contact', $request->input('pic_contact'));
        }

        if ($request->filled('status_affiliate')) {
            $query->where('status_affiliate', $request->input('status_affiliate'));
        }

        if ($request->filled('followersMin')) {
            $query->where('followers', '>=', (int) $request->input('followersMin'));
        }

        if ($request->filled('followersMax')) {
            $query->where('followers', '<=', (int) $request->input('followersMax'));
        }

        // Get data and calculate CPM for each
        $kols = $query->get();
        $exportData = $kols->map(function($kol) {
            $cpmData = $this->calculateCpmAndStatus($kol);
            $kol->cpm_calculated = $cpmData['cpm'];
            $kol->status_recommendation_calculated = $cpmData['status_recommendation'];
            return $kol;
        });

        return (new KeyOpinionLeaderExport())
            ->collection($exportData)
            ->download('kol-data-' . date('Y-m-d') . '.xlsx');
    }

    public function refreshFollowersFollowing(string $username): JsonResponse
    {
        $this->authorize('updateKOL', KeyOpinionLeader::class);
        
        $username = preg_replace('/\s*\(.*?\)\s*/', '', $username);
        
        // Add tenant filtering
        $user = Auth::user();
        $query = KeyOpinionLeader::where('username', $username);
        
        if ($user->hasRole(['client_1', 'client_2'])) {
            $query->where('tenant_id', $user->current_tenant_id);
        }
        
        $keyOpinionLeader = $query->first();

        if (!$keyOpinionLeader) {
            // Try to find similar KOL for channel reference
            $similarKolQuery = KeyOpinionLeader::where('username', 'LIKE', '%' . $username . '%');
            
            if ($user->hasRole(['client_1', 'client_2'])) {
                $similarKolQuery->where('tenant_id', $user->current_tenant_id);
            }
            
            $similarKol = $similarKolQuery->first();
            
            $keyOpinionLeader = KeyOpinionLeader::create([
                'username' => $username,
                'channel' => $similarKol ? $similarKol->channel : 'tiktok_video',
                'niche' => '-',
                'average_view' => 0,
                'content_type' => '-',
                'rate' => 0,
                'cpm' => 0,
                'created_by' => Auth::user()->id,
                'pic_contact' => Auth::user()->id,
                'followers' => 0,
                'following' => 0,
                'tenant_id' => Auth::user()->current_tenant_id,
            ]);
            
            if (!$keyOpinionLeader) {
                return response()->json(['error' => 'Key Opinion Leader not found'], 404);
            }
        }

        try {
            $channel = $keyOpinionLeader->channel;
            $url = '';
            $headers = [];

            if ($channel === CampaignContentEnum::TiktokVideo) {
                $url = "https://tokapi-mobile-version.p.rapidapi.com/v1/user/@{$username}";
                $headers = [
                    'x-rapidapi-host' => 'tokapi-mobile-version.p.rapidapi.com',
                    'x-rapidapi-key' => '2bc060ac02msh3d873c6c4d26f04p103ac5jsn00306dda9986',
                ];
            } elseif ($channel === CampaignContentEnum::InstagramFeed) {
                $url = "https://instagram-scraper-api2.p.rapidapi.com/v1/info?username_or_id_or_url={$username}";
                $headers = [
                    'x-rapidapi-host' => 'instagram-scraper-api2.p.rapidapi.com',
                    'x-rapidapi-key' => '2bc060ac02msh3d873c6c4d26f04p103ac5jsn00306dda9986',
                ];
            } else {
                return response()->json(['error' => 'Unsupported channel type'], 400);
            }

            $response = Http::withHeaders($headers)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                $followers = $data['user']['follower_count'] ?? 0;
                $following = $data['user']['following_count'] ?? 0;

                $keyOpinionLeader->update([
                    'followers' => $followers,
                    'following' => $following,
                ]);

                return response()->json(['followers' => $followers, 'following' => $following]);
            } else {
                return response()->json(['error' => 'Failed to fetch data'], $response->status());
            }
        } catch (Exception $e) {
            Log::error('Error refreshing followers: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }

    public function refreshFollowersFollowingSingle(string $username): JsonResponse
    {
        $this->authorize('updateKOL', KeyOpinionLeader::class);
        
        // Add tenant filtering
        $user = Auth::user();
        $query = KeyOpinionLeader::where('username', $username);
        
        if ($user->hasRole(['client_1', 'client_2'])) {
            $query->where('tenant_id', $user->current_tenant_id);
        }
        
        $keyOpinionLeader = $query->first();
        
        if (!$keyOpinionLeader) {
            return response()->json(['error' => 'Key Opinion Leader not found'], 404);
        }
        
        try {
            if ($keyOpinionLeader->channel === 'tiktok') {
                $url = "https://tokapi-mobile-version.p.rapidapi.com/v1/user/@{$username}";
                $headers = [
                    'x-rapidapi-host' => 'tokapi-mobile-version.p.rapidapi.com',
                    'x-rapidapi-key' => '2bc060ac02msh3d873c6c4d26f04p103ac5jsn00306dda9986',
                ];
            } elseif ($keyOpinionLeader->channel === 'instagram_feed') {
                $url = "https://instagram-scraper-api2.p.rapidapi.com/v1/info?username_or_id_or_url={$username}";
                $headers = [
                    'x-rapidapi-host' => 'instagram-scraper-api2.p.rapidapi.com',
                    'x-rapidapi-key' => '2bc060ac02msh3d873c6c4d26f04p103ac5jsn00306dda9986',
                ];
            } else {
                return response()->json(['error' => 'Unsupported channel type'], 400);
            }
            
            // Add debugging logs
            Log::info('Making API request for KOL: ' . $username, [
                'url' => $url,
                'channel' => $keyOpinionLeader->channel
            ]);
            
            $response = Http::withHeaders($headers)->get($url);
            
            // Log the raw API response
            Log::info('API Response for KOL: ' . $username, [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body(),
                'json' => $response->json()
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Log the parsed data structure
                Log::info('Parsed API data for KOL: ' . $username, [
                    'data_structure' => $data,
                    'has_user_key' => isset($data['user']),
                    'has_data_key' => isset($data['data'])
                ]);
                
                if ($keyOpinionLeader->channel === 'tiktok') {
                    $followers = $data['user']['follower_count'] ?? 0;
                    $following = $data['user']['following_count'] ?? 0;
                    $totalLikes = $data['user']['total_favorited'] ?? 0;
                    $videoCount = $data['user']['aweme_count'] ?? 0;
                    
                    // Log what we extracted
                    Log::info('Extracted TikTok data for KOL: ' . $username, [
                        'followers' => $followers,
                        'following' => $following,
                        'total_likes' => $totalLikes,
                        'video_count' => $videoCount,
                        'user_data_exists' => isset($data['user']),
                        'available_keys' => isset($data['user']) ? array_keys($data['user']) : []
                    ]);
                    
                    // Calculate engagement rate (likes-based)
                    $engagementRate = null;
                    if ($followers > 0 && $videoCount > 0) {
                        $avgLikesPerVideo = $totalLikes / $videoCount;
                        $engagementRate = round(($avgLikesPerVideo / $followers) * 100, 2);
                    }
                    
                } elseif ($keyOpinionLeader->channel === 'instagram_feed') {
                    $followers = $data['data']['follower_count'] ?? 0;
                    $following = $data['data']['following_count'] ?? 0;
                    $totalLikes = $data['data']['total_likes'] ?? 0;
                    $videoCount = $data['data']['media_count'] ?? 0;
                    
                    // Log what we extracted
                    Log::info('Extracted Instagram data for KOL: ' . $username, [
                        'followers' => $followers,
                        'following' => $following,
                        'total_likes' => $totalLikes,
                        'video_count' => $videoCount,
                        'data_exists' => isset($data['data']),
                        'available_keys' => isset($data['data']) ? array_keys($data['data']) : []
                    ]);
                    
                    // Calculate engagement rate for Instagram
                    $engagementRate = null;
                    if ($followers > 0 && $videoCount > 0) {
                        $avgLikesPerPost = $totalLikes / $videoCount;
                        $engagementRate = round(($avgLikesPerPost / $followers) * 100, 2);
                    }
                }
                
                $updateData = [
                    'followers' => $followers,
                    'following' => $following,
                    'total_likes' => $totalLikes,
                    'video_count' => $videoCount,
                ];
                
                // Only update engagement rate if we calculated it
                if ($engagementRate !== null) {
                    $updateData['engagement_rate'] = $engagementRate;
                }

                // Store old values for comparison
                $oldFollowers = $keyOpinionLeader->followers;
                $oldFollowing = $keyOpinionLeader->following;
                $oldTotalLikes = $keyOpinionLeader->total_likes;
                $oldVideoCount = $keyOpinionLeader->video_count;
                $oldEngagementRate = $keyOpinionLeader->engagement_rate;

                // Log the update data
                Log::info('Updating KOL data for: ' . $username, [
                    'update_data' => $updateData,
                    'old_followers' => $oldFollowers,
                    'new_followers' => $followers
                ]);

                // Recalculate CPM and status after updating
                $avgViews = $keyOpinionLeader->average_view ?: 0;
                $rate = $keyOpinionLeader->rate ?: 0;
                
                $keyOpinionLeader->update($updateData);
                
                return response()->json([
                    'success' => true,
                    'followers' => $followers,
                    'following' => $following,
                    'total_likes' => $totalLikes,
                    'video_count' => $videoCount,
                    'engagement_rate' => $engagementRate,
                    'kol_info' => [
                        'id' => $keyOpinionLeader->id,
                        'username' => $keyOpinionLeader->username,
                        'channel' => $keyOpinionLeader->channel,
                    ],
                    'changes' => [
                        'followers' => [
                            'old' => $oldFollowers,
                            'new' => $followers,
                            'difference' => $followers - ($oldFollowers ?: 0)
                        ],
                        'following' => [
                            'old' => $oldFollowing,
                            'new' => $following,
                            'difference' => $following - ($oldFollowing ?: 0)
                        ],
                        'total_likes' => [
                            'old' => $oldTotalLikes,
                            'new' => $totalLikes,
                            'difference' => $totalLikes - ($oldTotalLikes ?: 0)
                        ],
                        'video_count' => [
                            'old' => $oldVideoCount,
                            'new' => $videoCount,
                            'difference' => $videoCount - ($oldVideoCount ?: 0)
                        ],
                    ],
                    'message' => 'Follower and profile data updated successfully.',
                    'debug_info' => [
                        'api_url' => $url,
                        'api_status' => $response->status(),
                        'raw_response_size' => strlen($response->body())
                    ]
                ]);
                
            } else {
                Log::error('API request failed for KOL: ' . $username, [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'url' => $url
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to fetch data from API',
                    'api_status' => $response->status(),
                    'api_response' => $response->body()
                ], $response->status());
            }
            
        } catch (Exception $e) {
            Log::error('Error refreshing single KOL followers: ' . $e->getMessage(), [
                'username' => $username,
                'kol_id' => $keyOpinionLeader->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while refreshing follower data',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function importKeyOpinionLeaders(): JsonResponse
    {
        $this->authorize('createKOL', KeyOpinionLeader::class);
        
        try {
            $this->googleSheetService->setSpreadsheetId('11ob241Vwz7EuvhT0V9mBo7u_GDLSIkiVZ_sgKpQ4GfA');
            set_time_limit(0);
            $range = 'Sheet1!A2:H';
            $sheetData = $this->googleSheetService->getSheetData($range);

            $chunkSize = 50;
            $totalRows = count($sheetData);
            $processedRows = 0;
            $updatedRows = 0;
            $skippedRows = 0;
            $user = Auth::user();

            foreach (array_chunk($sheetData, $chunkSize) as $chunk) {
                foreach ($chunk as $row) {
                    // Skip if username (column C) is empty
                    if (empty($row[2])) {
                        $skippedRows++;
                        continue;
                    }
                    
                    // Process username from column C
                    $username = $this->processUsername($row[2]);
                    
                    // Skip if username processing failed
                    if (!$username) {
                        $skippedRows++;
                        continue;
                    }

                    $kolData = [
                        'username'         => $username,
                        'name'             => $row[3] ?? null,
                        'phone_number'     => $row[4] ?? null,
                        'address'          => $row[5] ?? null,
                        'niche'            => $row[6] ?? null,
                        'content_type'     => $row[7] ?? null,
                        'channel'          => 'tiktok_video',
                        'cpm'              => 0,
                        'followers'        => 0,
                        'following'        => 0,
                        'average_view'     => 0,
                        'rate'             => 0,
                        'created_by'       => $user->id,
                        'pic_contact'      => $user->id,
                        'tenant_id'        => $user->current_tenant_id,
                        'status_recommendation' => 'Worth it', // Default
                        'updated_at'       => now(),
                    ];

                    // Check for duplicate by username and tenant
                    $existingKolQuery = KeyOpinionLeader::where('username', $username);
                    
                    // Apply tenant filter for clients
                    if ($user->hasRole(['client_1', 'client_2'])) {
                        $existingKolQuery->where('tenant_id', $user->current_tenant_id);
                    }
                    
                    $existingKol = $existingKolQuery->first();

                    if ($existingKol) {
                        // Update existing record
                        $existingKol->update($kolData);
                        $updatedRows++;
                    } else {
                        // Create new record
                        $kolData['created_at'] = now();
                        KeyOpinionLeader::create($kolData);
                        $processedRows++;
                    }
                }
                usleep(100000); // Small delay to prevent overwhelming the server
            }

            return response()->json([
                'success' => true,
                'message' => 'Key Opinion Leaders imported successfully',
                'total_rows' => $totalRows,
                'processed_rows' => $processedRows,
                'updated_rows' => $updatedRows,
                'skipped_rows' => $skippedRows
            ]);
        } catch (\Exception $e) {
            Log::error('Error importing KOLs: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to import KOLs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process username from column C
     * Handle TikTok URLs, @ prefixed usernames, and plain usernames
     */
    private function processUsername($rawUsername)
    {
        if (empty($rawUsername)) {
            return null;
        }

        $username = trim($rawUsername);

        // Case 1: TikTok URL - extract username from URL
        if (strpos($username, 'tiktok.com/@') !== false) {
            preg_match('/tiktok\.com\/@([^?&\/]+)/', $username, $matches);
            if (isset($matches[1])) {
                return $matches[1];
            }
            return null;
        }

        // Case 2: Username with @ prefix - remove the @
        if (strpos($username, '@') === 0) {
            return substr($username, 1);
        }

        // Case 3: Plain username (like user2724318011378) - return as is
        return $username;
    }

    public function getBulkUsernames(Request $request): JsonResponse
    {
        $this->authorize('viewKOL', KeyOpinionLeader::class);
        
        $query = $this->getKOLDataTableQuery($request);
        $usernames = $query->whereIn('channel', ['tiktok_video'])
                        ->where('followers', 0)
                        ->whereDate('updated_at', '2025-06-04')
                        ->pluck('username')
                        ->toArray();

        return response()->json([
            'usernames' => $usernames,
            'count' => count($usernames)
        ]);
    }
    public function getCampaignHistory(string $username): JsonResponse
    {
        $this->authorize('viewKOL', KeyOpinionLeader::class);
        
        try {
            // Get campaign contents and statistics for this username
            $campaignHistory = DB::table('campaign_contents')
                ->join('campaigns', 'campaign_contents.campaign_id', '=', 'campaigns.id')
                ->leftJoin('statistics', function($join) {
                    $join->on('campaign_contents.id', '=', 'statistics.campaign_content_id')
                         ->whereRaw('statistics.date = (SELECT MAX(date) FROM statistics s2 WHERE s2.campaign_content_id = campaign_contents.id)');
                })
                ->where('campaign_contents.username', $username)
                ->select(
                    'campaigns.title as campaign_title',
                    'campaign_contents.task_name',
                    'campaign_contents.upload_date',
                    'campaign_contents.rate_card',
                    'statistics.view as views',
                    'statistics.like as likes',
                    'statistics.comment as comments',
                    'statistics.cpm'
                )
                ->orderBy('campaign_contents.upload_date', 'desc')
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $campaignHistory
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching campaign history: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch campaign history',
                'data' => []
            ], 500);
        }
    }
}