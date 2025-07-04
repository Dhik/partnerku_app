<?php

namespace App\Domain\Product\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Product\BLL\Product\ProductBLLInterface;
use App\Domain\Product\Models\Product;
use App\Domain\Product\Requests\ProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

/**
 * @property ProductBLLInterface productBLL
 */
class ProductController extends Controller
{
    public function __construct(ProductBLLInterface $productBLL)
    {
        $this->productBLL = $productBLL;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.product.index');
    }

    /**
     * Get data for DataTable
     */
    public function data()
    {
        $products = Product::where('tenant_id', Auth::user()->current_tenant_id)->get();
        return DataTables::of($products)
            ->addColumn('actions', function ($product) {
                return '
                    <button type="button" class="btn btn-sm btn-primary" onclick="showProduct(' . $product->id . ')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-success" onclick="editProduct(' . $product->id . ')">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteProduct(' . $product->id . ')">
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
    public function store(ProductRequest $request)
    {
        try {
            $data = $request->validated();
            $data['tenant_id'] = Auth::user()->current_tenant_id;
            
            Product::create($data);
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Check if product belongs to current tenant
        if ($product->tenant_id !== Auth::user()->current_tenant_id) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        // Check if product belongs to current tenant
        if ($product->tenant_id !== Auth::user()->current_tenant_id) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        try {
            // Check if product belongs to current tenant
            if ($product->tenant_id !== Auth::user()->current_tenant_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            $product->update($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            // Check if product belongs to current tenant
            if ($product->tenant_id !== Auth::user()->current_tenant_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            $product->delete();
            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product: ' . $e->getMessage()
            ], 500);
        }
    }
}