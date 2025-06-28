<?php

namespace App\Domain\Niche\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Niche\BLL\Niche\NicheBLLInterface;
use App\Domain\Niche\Models\Niche;
use App\Domain\Niche\Requests\NicheRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

/**
 * @property NicheBLLInterface nicheBLL
 */
class NicheController extends Controller
{
    public function __construct(NicheBLLInterface $nicheBLL)
    {
        $this->nicheBLL = $nicheBLL;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.niche.index');
    }

    /**
     * Get data for DataTable
     */
    public function data()
    {
        $niches = Niche::all();
        return DataTables::of($niches)
            ->addColumn('actions', function ($niche) {
                return '
                    <button type="button" class="btn btn-sm btn-primary" onclick="showNiche(' . $niche->id . ')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-success" onclick="editNiche(' . $niche->id . ')">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteNiche(' . $niche->id . ')">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                ';
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
    public function store(NicheRequest $request)
    {
        try {
            Niche::create($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Niche created successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating niche: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Niche $niche)
    {
        return response()->json([
            'success' => true,
            'data' => $niche
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Niche $niche)
    {
        return response()->json([
            'success' => true,
            'data' => $niche
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NicheRequest $request, Niche $niche)
    {
        try {
            $niche->update($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Niche updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating niche: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Niche $niche)
    {
        try {
            $niche->delete();
            return response()->json([
                'success' => true,
                'message' => 'Niche deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting niche: ' . $e->getMessage()
            ], 500);
        }
    }
}