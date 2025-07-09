<?php

namespace App\Domain\Income\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Income\BLL\Income\IncomeBLLInterface;
use App\Domain\Income\Models\Income;
use App\Domain\Income\Requests\IncomeRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use App\Domain\User\Models\User;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * @property IncomeBLLInterface incomeBLL
 */
class IncomeController extends Controller
{
    public function __construct(IncomeBLLInterface $incomeBLL)
    {
        $this->incomeBLL = $incomeBLL;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Get all users for the team selection dropdown
            $users = User::select('id', 'name', 'position', 'email')
                // ->whereNotNull('email_verified_at') // Commented out - include all users for now
                ->orderBy('name')
                ->get();

            $formattedUsers = $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name . ($user->position ? ' (' . $user->position . ')' : ''),
                    'email' => $user->email,
                    'position' => $user->position
                ];
            })->toArray();

            return view('admin.income.index', compact('formattedUsers'))->with('users', $formattedUsers);
        } catch (\Exception $e) {
            Log::error('Error loading users for income index:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return view with empty users array if there's an error
            return view('admin.income.index', ['users' => []])
                ->with('error', 'Failed to load team members. Please refresh the page.');
        }
    }

    /**
     * Get data for DataTable
     */
    public function data()
    {
        try {
            // Use query builder for better performance
            $incomes = Income::query();
            
            return DataTables::of($incomes)
                ->addColumn('actions', function ($income) {
                    return '
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-primary" onclick="showIncome(' . $income->id . ')" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-success" onclick="editIncome(' . $income->id . ')" title="Edit">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteIncome(' . $income->id . ')" title="Delete">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    ';
                })
                ->editColumn('date', function ($income) {
                    return $income->date ? Carbon::parse($income->date)->format('d-m-Y') : '-';
                })
                ->editColumn('revenue_contract', function ($income) {
                    return 'Rp ' . number_format($income->revenue_contract, 0, ',', '.');
                })
                ->editColumn('team_in_charge', function ($income) {
                    return $this->formatTeamMembers($income->team_in_charge);
                })
                ->rawColumns(['actions'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error loading income data:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to load data'
            ], 500);
        }
    }

    /**
     * Format team members for display in DataTable
     */
    private function formatTeamMembers($teamInCharge)
    {
        try {
            if (empty($teamInCharge)) {
                return '<span class="text-muted">No team assigned</span>';
            }

            // Normalize the data to array
            $userIds = $this->normalizeTeamInCharge($teamInCharge);

            if (empty($userIds)) {
                return '<span class="text-muted">No team assigned</span>';
            }

            // Get user names with positions
            $users = User::whereIn('id', $userIds)
                ->select('name', 'position')
                ->orderBy('name')
                ->get()
                ->map(function ($user) {
                    return $user->name . ($user->position ? ' (' . $user->position . ')' : '');
                })
                ->toArray();

            if (empty($users)) {
                return '<span class="text-warning">Unknown users</span>';
            }

            // Limit display to avoid clutter in table
            if (count($users) > 3) {
                $displayUsers = array_slice($users, 0, 3);
                $remaining = count($users) - 3;
                return implode(', ', $displayUsers) . ' <small class="text-muted">+' . $remaining . ' more</small>';
            }

            return implode(', ', $users);
        } catch (\Exception $e) {
            Log::error('Error formatting team members', [
                'team_in_charge' => $teamInCharge,
                'error' => $e->getMessage()
            ]);
            return '<span class="text-danger">Error loading team</span>';
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json(['success' => true]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(IncomeRequest $request)
    {
        try {
            $validatedData = $request->validated();
            
            // Process team_in_charge data
            $validatedData['team_in_charge'] = $this->processTeamInCharge($validatedData['team_in_charge'] ?? []);
            
            $income = Income::create($validatedData);
            
            return response()->json([
                'success' => true,
                'message' => 'Income created successfully!',
                'data' => $income
            ]);
        } catch (\Exception $e) {
            Log::error('Income creation error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating income: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Income $income)
    {
        try {
            // Get detailed team member information
            $teamMembers = $this->getDetailedTeamMembers($income->team_in_charge);
            
            $incomeData = $income->toArray();
            $incomeData['team_members_names'] = array_column($teamMembers, 'display_name');
            $incomeData['team_members_details'] = $teamMembers;
            
            return response()->json([
                'success' => true,
                'data' => $incomeData
            ]);
        } catch (\Exception $e) {
            Log::error('Error showing income:', [
                'income_id' => $income->id,
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading income details'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Income $income)
    {
        try {
            $incomeData = $income->toArray();
            
            // Ensure team_in_charge is always an array of strings for the frontend
            $normalizedTeam = $this->normalizeTeamInCharge($income->team_in_charge);
            $incomeData['team_in_charge'] = array_map('strval', $normalizedTeam);
            
            return response()->json([
                'success' => true,
                'data' => $incomeData
            ]);
        } catch (\Exception $e) {
            Log::error('Error editing income:', [
                'income_id' => $income->id,
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading income for editing'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(IncomeRequest $request, Income $income)
    {
        try {
            $validatedData = $request->validated();
            
            // Process team_in_charge data
            $validatedData['team_in_charge'] = $this->processTeamInCharge($validatedData['team_in_charge'] ?? []);
            
            $income->update($validatedData);
            
            return response()->json([
                'success' => true,
                'message' => 'Income updated successfully!',
                'data' => $income->fresh()
            ]);
        } catch (\Exception $e) {
            Log::error('Income update error:', [
                'income_id' => $income->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating income: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Income $income)
    {
        try {
            $incomeId = $income->id;
            
            $income->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Income deleted successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Income deletion error:', [
                'income_id' => $income->id,
                'message' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting income: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process team_in_charge data for storage
     */
    private function processTeamInCharge($teamInCharge)
    {
        try {
            if (empty($teamInCharge)) {
                return [];
            }

            // Handle different input types
            if (is_string($teamInCharge)) {
                // If it's a JSON string, decode it
                $decoded = json_decode($teamInCharge, true);
                $teamInCharge = is_array($decoded) ? $decoded : [$teamInCharge];
            } elseif (!is_array($teamInCharge)) {
                // If it's a single value, convert to array
                $teamInCharge = [$teamInCharge];
            }

            // Filter out empty values and ensure they're integers
            $teamInCharge = array_filter(array_map('intval', array_filter($teamInCharge)));

            if (empty($teamInCharge)) {
                return [];
            }

            // Validate that all user IDs exist in the users table
            $validUserIds = User::whereIn('id', $teamInCharge)
                // ->whereNotNull('email_verified_at') // Commented out - include all users for now
                ->pluck('id')
                ->toArray();

            if (count($validUserIds) !== count($teamInCharge)) {
                $invalidIds = array_diff($teamInCharge, $validUserIds);
                Log::warning('Invalid or unverified user IDs provided for team_in_charge:', [
                    'invalid_ids' => $invalidIds,
                    'valid_ids' => $validUserIds,
                    'submitted_ids' => $teamInCharge
                ]);
            }

            return array_values($validUserIds); // Return only valid user IDs
        } catch (\Exception $e) {
            Log::error('Error processing team_in_charge', [
                'input' => $teamInCharge,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Normalize team_in_charge to always return an array
     */
    private function normalizeTeamInCharge($teamInCharge)
    {
        try {
            if (empty($teamInCharge)) {
                return [];
            }

            if (is_string($teamInCharge)) {
                $decoded = json_decode($teamInCharge, true);
                return is_array($decoded) ? array_map('intval', $decoded) : [intval($teamInCharge)];
            }

            if (is_array($teamInCharge)) {
                return array_map('intval', array_filter($teamInCharge));
            }

            return [intval($teamInCharge)];
        } catch (\Exception $e) {
            Log::error('Error normalizing team_in_charge', [
                'input' => $teamInCharge,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get detailed team member information
     */
    private function getDetailedTeamMembers($teamInCharge)
    {
        try {
            $userIds = $this->normalizeTeamInCharge($teamInCharge);
            
            if (empty($userIds)) {
                return [];
            }

            $users = User::whereIn('id', $userIds)
                ->select('id', 'name', 'email', 'position', 'phone_number')
                ->orderBy('name')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'position' => $user->position,
                        'phone_number' => $user->phone_number,
                        'display_name' => $user->name . ($user->position ? ' (' . $user->position . ')' : '')
                    ];
                })
                ->toArray();

            return $users;
        } catch (\Exception $e) {
            Log::error('Error getting detailed team members', [
                'team_in_charge' => $teamInCharge,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
}