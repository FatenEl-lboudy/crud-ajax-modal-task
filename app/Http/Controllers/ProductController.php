<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')->latest()->get();

        // Check if the incoming request is an AJAX call (DataTables sends requests this way)
        if ($request->ajax()) {
            // Pass the $products collection to Yajra DataTables to build the table
            return DataTables::of($products)

                //add a custom "actions" column that isn't a real DB column
                ->addColumn('actions', function ($row) {
                    return '<a href="javascript:void(0)" class="btn-sm btn btn-info editButton mr-1" data-id="' . $row->id . '">Edit</a>
                <a href="javascript:void(0)" class="btn-sm btn btn-danger delButton" data-id="' . $row->id . '">Del</a>';
                })
                // tell DataTables NOT to escape the HTML in the "actions" column or it get rendered as plain text
                ->rawColumns(['actions'])
                ->make(true);
        }
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('products.create', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        if ($request->category_id) {
            $product = Product::find($request->category_id);
            if(! $product){
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
            'name'     => $product->name,
            'type'     => $product->category_id,
            'price'    => $product->price,
            'stock'    => $product->stock_qty,
        ]);
    }
}
