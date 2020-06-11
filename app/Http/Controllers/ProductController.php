<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Product;

class ProductController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index()
    {
        $products = Product::with('category')->orderBy('category_id', 'desc')->get();
        if ($products->isEmpty()) {
            return response()->json([
                'error' => true,
                'message' => 'GetProduct: FAILED, product empty',
                'data' => null
            ]);
        }else {
            return response()->json([
                'error' => false,
                'message' => 'GetProduct: SUCCESS',
                'data' => $products
            ]);
        }
    }

    public function getProductAvailable($category_id)
    {
        if ($category_id != 0) {
            $products = Product::with('category')->where([['stock', '>', '0'], ['category_id', '=', $category_id]])->orderBy('category_id', 'desc')->get();
        } else {
            $products = Product::with('category')->where('stock', '>', '0')->orderBy('category_id', 'desc')->get();
        }
        
        if ($products->isEmpty()) {
            return response()->json([
                'error' => true,
                'message' => 'GetProductAvailable: FAILED, product empty',
                'data' => null
            ]);
        }else {
            return response()->json([
                'error' => false,
                'message' => 'GetProductAvailable: SUCCESS',
                'data' => $products
            ]);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => ['required', 'numeric'],
            'name' => ['required', 'string'],
            'price' => ['required', 'numeric'],
            'stock' => ['required', 'numeric']
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'CreateProduct: FAILED',
                'errors' => $validator->errors(),
                'data' => null
            ]);
        }else {
            $product = Product::create([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'price' => $request->price,
                'stock' => $request->stock
            ]);

            return response()->json([
                'error' => false,
                'message' => 'CreateProduct: SUCCESS',
                'data' => $product
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => ['required', 'numeric'],
            'name' => ['required', 'string'],
            'price' => ['required', 'numeric'],
            'stock' => ['required', 'numeric']
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'EditProduct: FAILED',
                'errors' => $validator->errors(),
                'data' => null
            ]);
        }else {
            $product = Product::find($id);
            $product->update([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'price' => $request->price,
                'stock' => $request->stock
            ]);

            return response()->json([
                'error' => false,
                'message' => 'EditProduct: SUCCESS',
                'data' => $product
            ]);
        }
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if ($product->transaction->isEmpty()) {
            $product->delete();
            return response()->json([
                'error' => false,
                'message' => 'DeleteProduct: SUCCESS',
                'data' => $product
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, please delete all transactions with this product first!',
                'data' => $product
            ]);
        }     
    }
}
