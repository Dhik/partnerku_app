<?php

namespace App\Domain\Payroll\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Payroll\BLL\Payroll\PayrollBLLInterface;
use App\Domain\Payroll\Models\Payroll;
use App\Domain\Payroll\Requests\PayrollRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

/**
 * @property PayrollBLLInterface payrollBLL
 */
class PayrollController extends Controller
{
    public function __construct(PayrollBLLInterface $payrollBLL)
    {
        $this->payrollBLL = $payrollBLL;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.payroll.index');
    }

    /**
     * Get data for DataTable
     */
    public function data()
    {
        $payrolls = Payroll::all();
        return DataTables::of($payrolls)
            ->addColumn('actions', function ($payroll) {
                return '
                    <button type="button" class="btn btn-sm btn-primary" onclick="showPayroll(' . $payroll->id . ')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-success" onclick="editPayroll(' . $payroll->id . ')">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deletePayroll(' . $payroll->id . ')">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                ';
            })
            ->editColumn('salary', function ($payroll) {
                return 'Rp ' . number_format($payroll->salary, 2, ',', '.');
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
    public function store(PayrollRequest $request)
    {
        try {
            Payroll::create($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Payroll created successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating payroll: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Payroll $payroll)
    {
        return response()->json([
            'success' => true,
            'data' => $payroll
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payroll $payroll)
    {
        return response()->json([
            'success' => true,
            'data' => $payroll
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PayrollRequest $request, Payroll $payroll)
    {
        try {
            $payroll->update($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Payroll updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating payroll: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payroll $payroll)
    {
        try {
            $payroll->delete();
            return response()->json([
                'success' => true,
                'message' => 'Payroll deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting payroll: ' . $e->getMessage()
            ], 500);
        }
    }
}