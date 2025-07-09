<?php

namespace App\Domain\OtherSpent\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\OtherSpent\BLL\OtherSpent\OtherSpentBLLInterface;
use App\Domain\OtherSpent\Models\OtherSpent;
use App\Domain\Income\Models\Income;
use App\Domain\OtherSpent\Requests\OtherSpentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

/**
 * @property OtherSpentBLLInterface otherSpentBLL
 */
class OtherSpentController extends Controller
{
    public function __construct(OtherSpentBLLInterface $otherSpentBLL)
    {
        $this->otherSpentBLL = $otherSpentBLL;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.other_spent.index');
    }

    /**
     * Get data for DataTable
     */
    public function data()
    {
        $otherSpents = OtherSpent::all();
        return DataTables::of($otherSpents)
            ->addColumn('actions', function ($otherSpent) {
                return '
                    <button type="button" class="btn btn-sm btn-primary" onclick="showOtherSpent(' . $otherSpent->id . ')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-success" onclick="editOtherSpent(' . $otherSpent->id . ')">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteOtherSpent(' . $otherSpent->id . ')">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                ';
            })
            ->editColumn('date', function ($otherSpent) {
                return Carbon::parse($otherSpent->date)->format('d-m-Y');
            })
            ->editColumn('amount', function ($otherSpent) {
                return 'Rp ' . number_format($otherSpent->amount, 2, ',', '.');
            })
            ->editColumn('type', function ($otherSpent) {
                return $otherSpent->type ?: '-';
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
    public function store(OtherSpentRequest $request)
    {
        try {
            OtherSpent::create($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Other spent created successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating other spent: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(OtherSpent $otherSpent)
    {
        return response()->json([
            'success' => true,
            'data' => $otherSpent
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OtherSpent $otherSpent)
    {
        return response()->json([
            'success' => true,
            'data' => $otherSpent
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OtherSpentRequest $request, OtherSpent $otherSpent)
    {
        try {
            $otherSpent->update($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Other spent updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating other spent: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OtherSpent $otherSpent)
    {
        try {
            $otherSpent->delete();
            return response()->json([
                'success' => true,
                'message' => 'Other spent deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting other spent: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Display cashflow advice page
     */
    public function calculations()
    {
        return view('admin.other_spent.calculations');
    }

    /**
     * Get cashflow calculations data
     */
    public function calculationsData(Request $request)
    {
        try {
            $month = $request->get('month', Carbon::now()->format('Y-m'));
            $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

            // Get total revenue for the month
            $totalRevenue = Income::whereNotNull('date')
                ->whereBetween('date', [$startDate, $endDate])
                ->sum('revenue_contract');

            // Recommendation percentages
            $recommendations = [
                'Sales Marketing' => 12,
                'Payroll' => 17,
                'Utilities' => 1.5,
                'Admin and General' => 2,
                'Learning and Development' => 1.25,
                'THR' => 1.25,
                'Other' => 5
            ];

            // Calculate recommended amounts
            $recommendedAmounts = [];
            $totalRecommended = 0;
            foreach ($recommendations as $type => $percentage) {
                $amount = ($totalRevenue * $percentage) / 100;
                $recommendedAmounts[$type] = $amount;
                $totalRecommended += $amount;
            }

            // Get actual spending by type for the month
            $actualSpending = OtherSpent::whereBetween('date', [$startDate, $endDate])
                ->select('type', DB::raw('SUM(amount) as total_amount'))
                ->groupBy('type')
                ->pluck('total_amount', 'type')
                ->toArray();

            // Calculate total actual spending
            $totalActualSpending = array_sum($actualSpending);

            // Combine data for response
            $expenseData = [];
            foreach ($recommendations as $type => $percentage) {
                $recommended = $recommendedAmounts[$type];
                $actual = $actualSpending[$type] ?? 0;
                
                // Determine status color
                $status = 'on-target'; // green
                if ($actual < $recommended) {
                    $status = 'under-budget'; // blue
                } elseif ($actual > $recommended) {
                    $status = 'over-budget'; // red
                }

                $expenseData[] = [
                    'type' => $type,
                    'percentage' => $percentage,
                    'recommended' => $recommended,
                    'actual' => $actual,
                    'status' => $status,
                    'difference' => $actual - $recommended,
                    'percentage_used' => $recommended > 0 ? ($actual / $recommended) * 100 : 0
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'month' => $month,
                    'monthName' => $startDate->format('F Y'),
                    'totalRevenue' => $totalRevenue,
                    'totalRecommended' => $totalRecommended,
                    'totalActualSpending' => $totalActualSpending,
                    'totalDifference' => $totalActualSpending - $totalRecommended,
                    'expenseData' => $expenseData,
                    'budgetUtilization' => $totalRecommended > 0 ? ($totalActualSpending / $totalRecommended) * 100 : 0
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading calculations: ' . $e->getMessage()
            ], 500);
        }
    }
}