<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Pagination\Paginator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Paginator::useBootstrapFour();
        $products = Product::with('category')->latest();

        if ($request->filled('category_id')) {
            $categoryIds = Category::where('id', $request->category_id)
                ->orWhere('parent_id', $request->category_id)
                ->pluck('id');

            $products->whereIn('category_id', $categoryIds);
        }

        if ($request->filled('search')) {
            $products->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $products->paginate(10)->appends($request->query());


        // Check if the incoming request is an AJAX call (DataTables sends requests this way)
        if ($request->ajax()) {
            
            return response()->json([
            'table' => view('products.partials.table-body', compact('products'))->render(),
            'pagination' => $products->links()->toHtml(),
        ]);
        }

        $categories = Category::whereNull('parent_id')->with('children.children')->get();
        return view('products.create', compact('products', 'categories'));
    }

    // public function create()
    // {
    //      $categories = Category::orderBy('name')->get();
    //      return view('products.create', ['categories' => $categories]);
    // }

    public function store(Request $request)
    {
        if ($request->product_id) {
            $product = Product::find($request->product_id);
            if (! $product) {
                abort(404);
            }

            $validated = $request->validate([
                'name'        => 'required|string| max:255',
                'type' => 'required|integer|exists:categories,id',
                'price'       => 'required|numeric|min:0',
                'stock'       => 'required|integer|min:0',
            ]);

            $product->update([
                'category_id' => $validated['type'],
                'name' => $validated['name'],
                'price' => $validated['price'],
                'stock_qty' => $validated['stock'],
            ]);

            return response()->json([
                'success' => 'Product updated successfully.',
                'product' => $product->load('category'),
            ], 200);
        } else {
            $validated = $request->validate([
                'name'        => 'required|string| max:255',
                'type' => 'required|integer|exists:categories,id',
                'price'       => 'required|numeric|min:0',
                'stock'       => 'required|integer|min:0',
            ]);

            $product = Product::create([
                'category_id' => $validated['type'],
                'name' => $validated['name'],
                'price' => $validated['price'],
                'stock_qty' => $validated['stock'],
            ]);

            return response()->json([
                'success' => 'Product created successfully.',
                'product' => $product->load('category'),
            ], 201);
        }
    }

    public function edit($id)
    {
        $product = Product::find($id);
        if (!$product) {
            abort(404);
        }
        return response()->json([
            'id'    => $product->id,
            'name'     => $product->name,
            'type'     => $product->category_id,
            'price'    => $product->price,
            'stock'    => $product->stock_qty,
        ]);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            abort(404);
        }
        $product->delete();
        return response()->json([
            'success' => 'Product deleted successfully.',
        ], 200);
    }

    public function multiDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:products,id',
        ]);

        Product::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => count($request->ids) . ' product(s) deleted successfully.',
        ], 200);
    }
}
