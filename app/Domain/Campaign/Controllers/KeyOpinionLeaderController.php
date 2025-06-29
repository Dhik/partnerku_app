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
        $marketingUsers = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['tim_internal', 'tim_ads', 'superadmin']);
        })->get();

        return compact('channels', 'niches', 'skinTypes', 'skinConcerns', 'contentTypes', 'marketingUsers');
    }

    /**
     * Get KOL datatable query
     */
    protected function getKOLDataTableQuery(Request $request)
    {
        $query = KeyOpinionLeader::with(['picContact', 'createdBy'])
            ->select('key_opinion_leaders.*');

        // Apply filters based on user role and tenant
        $user = Auth::user();
        if ($user->hasRole(['client_1', 'client_2'])) {
            $query->where('tenant_id', $user->current_tenant_id);
        }

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

        if ($request->filled('status_affiliate')) {
            $query->where('status_affiliate', $request->input('status_affiliate'));
        }

        if ($request->filled('followersMin')) {
            $query->where('followers', '>=', (int) $request->input('followersMin'));
        }

        if ($request->filled('followersMax')) {
            $query->where('followers', '<=', (int) $request->input('followersMax'));
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
    public function get(Request $request): JsonResponse
    {
        $this->authorize('viewKOL', KeyOpinionLeader::class);

        $query = $this->getKOLDataTableQuery($request);

        return DataTables::of($query)
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
                $badgeClass = $cpmData['status_recommendation'] === 'Worth it' ? 'badge-success' : 'badge-danger';
                return '<span class="badge ' . $badgeClass . '">' . $cpmData['status_recommendation'] . '</span>';
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

    public function getKpiData(Request $request): JsonResponse
    {
        $this->authorize('viewKOL', KeyOpinionLeader::class);
        
        $query = $this->getKOLDataTableQuery($request);
        $filteredKols = $query->get();
        
        $totalKol = $filteredKols->count();
        $worthItCount = 0;
        $totalCpm = 0;
        $validCpmCount = 0;
        
        foreach ($filteredKols as $kol) {
            $cpmData = $this->calculateCpmAndStatus($kol);
            if ($cpmData['status_recommendation'] === 'Worth it') {
                $worthItCount++;
            }
            if ($cpmData['cpm'] > 0) {
                $totalCpm += $cpmData['cpm'];
                $validCpmCount++;
            }
        }
        
        $avgCpm = $validCpmCount > 0 ? $totalCpm / $validCpmCount : 0;
        $worthItPercentage = $totalKol > 0 ? ($worthItCount / $totalKol) * 100 : 0;
        
        return response()->json([
            'total_kol' => $totalKol,
            'worth_it_count' => $worthItCount,
            'worth_it_percentage' => round($worthItPercentage, 2),
            'avg_cpm' => round($avgCpm, 2),
            'total_followers' => $filteredKols->sum('followers')
        ]);
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
            
            // Calculate CPM and status
            $avgViews = $data['average_view'] ?? 0;
            $rate = $data['rate'] ?? 0;
            $cpm = $avgViews > 0 ? ($rate / $avgViews) * 1000 : 0;
            $data['cpm'] = round($cpm, 2);
            $data['status_recommendation'] = $cpm < 25000 ? 'Worth it' : 'Gagal';

            $kol = KeyOpinionLeader::create($data);
            
            return redirect()
                ->route('kol.show', $kol->id)
                ->with([
                    'alert' => 'success',
                    'message' => trans('messages.success_save', ['model' => trans('labels.key_opinion_leader')]),
                ]);
        } catch (\Exception $e) {
            Log::error('Error creating KOL: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create KOL.']);
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
            
            // Calculate CPM and status
            $avgViews = $data['average_view'] ?? $keyOpinionLeader->average_view ?? 0;
            $rate = $data['rate'] ?? $keyOpinionLeader->rate ?? 0;
            $cpm = $avgViews > 0 ? ($rate / $avgViews) * 1000 : 0;
            $data['cpm'] = round($cpm, 2);
            $data['status_recommendation'] = $cpm < 25000 ? 'Worth it' : 'Gagal';
            
            $keyOpinionLeader->update($data);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('messages.success_update', ['model' => trans('labels.key_opinion_leader')]),
                    'data' => $keyOpinionLeader->fresh()
                ]);
            }
            
            return redirect()
                ->route('kol.show', $keyOpinionLeader->id)
                ->with([
                    'alert' => 'success',
                    'message' => trans('messages.success_update', ['model' => trans('labels.key_opinion_leader')]),
                ]);
                
        } catch (\Exception $e) {
            Log::error('Error updating KOL: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update KOL information.'
                ], 500);
            }
            
            return redirect()->back()->withErrors(['error' => 'Failed to update KOL information.']);
        }
    }

    /**
     * Delete KOL
     */
    public function destroy(KeyOpinionLeader $keyOpinionLeader): JsonResponse
    {
        $this->authorize('deleteKOL', KeyOpinionLeader::class);

        try {
            $keyOpinionLeader->delete();
            
            return response()->json([
                'success' => true,
                'message' => trans('messages.success_delete', ['model' => trans('labels.key_opinion_leader')])
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting KOL: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete KOL.'
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
            if ($keyOpinionLeader->channel === 'tiktok_video') {
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
            
            $response = Http::withHeaders($headers)->get($url);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if ($keyOpinionLeader->channel === 'tiktok_video') {
                    $followers = $data['user']['follower_count'] ?? 0;
                    $following = $data['user']['following_count'] ?? 0;
                    $totalLikes = $data['user']['total_favorited'] ?? 0;
                    $videoCount = $data['user']['aweme_count'] ?? 0;
                    
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

                // Recalculate CPM and status after updating
                $avgViews = $keyOpinionLeader->average_view ?: 0;
                $rate = $keyOpinionLeader->rate ?: 0;
                $cpm = $avgViews > 0 ? ($rate / $avgViews) * 1000 : 0;
                $updateData['cpm'] = round($cpm, 2);
                $updateData['status_recommendation'] = $cpm < 25000 ? 'Worth it' : 'Gagal';
                
                $keyOpinionLeader->update($updateData);
                
                return response()->json([
                    'followers' => $followers,
                    'following' => $following,
                    'total_likes' => $totalLikes,
                    'video_count' => $videoCount,
                    'engagement_rate' => $engagementRate,
                    'cpm' => $updateData['cpm'],
                    'status_recommendation' => $updateData['status_recommendation'],
                    'message' => 'Profile data updated successfully.',
                ]);
                
            } else {
                return response()->json(['error' => 'Failed to fetch data from API'], $response->status());
            }
            
        } catch (Exception $e) {
            Log::error('Error refreshing single KOL: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while refreshing data', 'details' => $e->getMessage()], 500);
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