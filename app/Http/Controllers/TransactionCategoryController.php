<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransactionCategory;
use App\Http\Resources\TransactionCategoryResource;

class TransactionCategoryController extends Controller
{
    public function index()
    {
        $categories = TransactionCategory::all();
        return response()->json([
            'transaction categories' => TransactionCategoryResource::collection($categories)
        ]);
    }

    public function show($id)
    {
        $category = TransactionCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json([
            'transaction category' => new TransactionCategoryResource($category) 
        ]);
    }
}

