<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401);
        }

        $categories = Auth::user()->categories()->get();
        
        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'display_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $existingCategory = Auth::user()->categories()
            ->where('name', strtoupper($request->name))
            ->first();

        if ($existingCategory) {
            return response()->json([
                'success' => false,
                'message' => 'This category already exists!'
            ], 409);
        }

        $category = Auth::user()->categories()->create([
            'name' => strtoupper($request->name),
            'display_name' => $request->display_name,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'category' => $category
        ]);
    }

    public function destroy($id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401);
        }

        $category = Auth::user()->categories()->find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }

    public function migrate(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'categories' => 'required|array',
            'categories.*.name' => 'required|string',
            'categories.*.description' => 'nullable|string',
            'categories.*.createdAt' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $migratedCount = 0;
        $userId = Auth::id();

        foreach ($request->categories as $categoryData) {
            $exists = Category::where('user_id', $userId)
                ->where('name', $categoryData['name'])
                ->exists();

            if (!$exists) {
                Category::create([
                    'user_id' => $userId,
                    'name' => $categoryData['name'],
                    'display_name' => $categoryData['description'] ?? null,
                    'description' => null,
                    'created_at' => $categoryData['createdAt'] ?? now(),
                    'updated_at' => now(),
                ]);
                $migratedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Migrated {$migratedCount} categories to database",
            'migrated_count' => $migratedCount
        ]);
    }
}