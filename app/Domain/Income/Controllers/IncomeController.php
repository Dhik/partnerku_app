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
            Income::create($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Income created successfully!'
            ]);
        } catch (\Exception $e) {
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
            $income->update($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Income updated successfully!'
            ]);
        } catch (\Exception $e) {
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