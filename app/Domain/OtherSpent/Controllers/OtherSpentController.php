<?php

namespace App\Domain\OtherSpent\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\OtherSpent\BLL\OtherSpent\OtherSpentBLLInterface;
use App\Domain\OtherSpent\Models\OtherSpent;
use App\Domain\OtherSpent\Requests\OtherSpentRequest;
use Illuminate\Http\Request;
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
}
