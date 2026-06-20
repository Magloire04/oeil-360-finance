<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::orderBy('name')->get();

        return ApiResponse::success($categories);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense,both',
        ]);

        $category = Category::create($validated);

        return ApiResponse::success($category, null, 201);
    }

    public function show(Category $category): JsonResponse
    {
        return ApiResponse::success($category);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:income,expense,both',
        ]);

        $category->update($validated);

        return ApiResponse::success($category);
    }

    public function destroy(Category $category): JsonResponse|Response
    {
        if ($category->isUsed()) {
            $category->update(['is_archived' => true]);

            return ApiResponse::success(['archived' => true]);
        }

        $category->delete();

        return response()->noContent();
    }

    public function restore(Category $category): JsonResponse
    {
        $category->update(['is_archived' => false]);

        return ApiResponse::success($category);
    }
}
