<?php

namespace App\Domain\Income\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Income\BLL\Income\IncomeBLLInterface;
use App\Domain\Income\Models\Income;
use App\Domain\Income\Requests\IncomeRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;

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
        return view('admin.income.index');
    }

    /**
     * Get users for dropdown
     */
    public function getUsers()
    {
        // Assuming you have a User model, adjust the query as needed
        $users = \App\Models\User::select('id', 'name')->get();
        return response()->json($users);
    }

    /**
     * Get data for DataTable
     */
    public function data()
    {
        $incomes = Income::all();
        return DataTables::of($incomes)
            ->addColumn('actions', function ($income) {
                return '
                    <button type="button" class="btn btn-sm btn-primary" onclick="showIncome(' . $income->id . ')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-success" onclick="editIncome(' . $income->id . ')">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteIncome(' . $income->id . ')">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                ';
            })
            ->editColumn('revenue_contract', function ($income) {
                return 'Rp ' . number_format($income->revenue_contract, 2, ',', '.');
            })
            ->editColumn('team_in_charge', function ($income) {
                // Display team members as comma-separated names
                if (is_array($income->team_in_charge)) {
                    $userIds = $income->team_in_charge;
                    $users = \App\Models\User::whereIn('id', $userIds)->pluck('name')->toArray();
                    return implode(', ', $users);
                }
                return $income->team_in_charge;
            })
            ->rawColumns(['actions'])
            ->make(true);
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
            
            // Convert team_in_charge array to JSON if it's an array
            if (isset($validatedData['team_in_charge']) && is_array($validatedData['team_in_charge'])) {
                $validatedData['team_in_charge'] = $validatedData['team_in_charge'];
            } elseif (isset($validatedData['team_in_charge'])) {
                // If it's a single value, convert to array
                $validatedData['team_in_charge'] = [$validatedData['team_in_charge']];
            }
            
            // Debug: Log the data being saved
            \Log::info('Income data to be saved:', $validatedData);
            
            Income::create($validatedData);
            
            return response()->json([
                'success' => true,
                'message' => 'Income created successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Income creation error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
        // Get user names for team_in_charge display
        $teamMembers = [];
        if (is_array($income->team_in_charge)) {
            $teamMembers = \App\Models\User::whereIn('id', $income->team_in_charge)->pluck('name')->toArray();
        }
        
        $income->team_members_names = $teamMembers;
        
        return response()->json([
            'success' => true,
            'data' => $income
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Income $income)
    {
        return response()->json([
            'success' => true,
            'data' => $income
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(IncomeRequest $request, Income $income)
    {
        try {
            $validatedData = $request->validated();
            
            // Convert team_in_charge array to JSON if it's an array
            if (isset($validatedData['team_in_charge']) && is_array($validatedData['team_in_charge'])) {
                $validatedData['team_in_charge'] = $validatedData['team_in_charge'];
            } elseif (isset($validatedData['team_in_charge'])) {
                // If it's a single value, convert to array
                $validatedData['team_in_charge'] = [$validatedData['team_in_charge']];
            }
            
            // Debug: Log the data being updated
            \Log::info('Income data to be updated:', $validatedData);
            
            $income->update($validatedData);
            
            return response()->json([
                'success' => true,
                'message' => 'Income updated successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Income update error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
            $income->delete();
            return response()->json([
                'success' => true,
                'message' => 'Income deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting income: ' . $e->getMessage()
            ], 500);
        }
    }
}