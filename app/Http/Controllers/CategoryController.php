<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Category;

class CategoryController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index()
    {
        $category = Category::all();
        if ($category->isEmpty()) {
            return response()->json([
                'error' => true,
                'message' => 'GetCategory: FAILED, category empty',
                'data' => null
            ]);
        }else {
            return response()->json([
                'error' => false,
                'message' => 'GetCategory: SUCCESS',
                'data' => $category
            ]);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'unique:category']
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'CreateCategory: FAILED',
                'errors' => $validator->errors(),
                'data' => null
            ]);
        }else {
            $category = Category::create([
                'name' => $request->name
            ]);
    
            return response()->json([
                'error' => false,
                'message' => 'CreateCategory: SUCCESS',
                'data' => $category
            ]);            
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'unique:category']
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'EditCategory: FAILED',
                'errors' => $validator->errors(),
                'data' => null
            ]);
        }else {
            $category = Category::find($id);
            $category->update([
                'name' => $request->name
            ]);
    
            return response()->json([
                'error' => false,
                'message' => 'EditCategory: SUCCESS',
                'data' => $category
            ]);            
        }
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if  (!$category->product->isEmpty()) {
            return response()->json([
                'error' => true,
                'message' => 'Failed, please delete all products with this category first!',
                'data' => $category
            ]);  
        } else {
            $category->delete();
            return response()->json([
                'error' => false,
                'message' => 'DeleteCategory: SUCCESS',
                'data' => $category
            ]);  
        } 
    }
}