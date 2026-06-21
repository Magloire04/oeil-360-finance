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
        $categories = Category::where('user_id', auth()->id())
            ->orderBy('name')
            ->get();

        return ApiResponse::success($categories);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense,both',
        ]);

        $category = Category::create(['user_id' => auth()->id()] + $validated);

        return ApiResponse::success($category, null, 201);
    }

    public function show(int $id): JsonResponse
    {
        $category = Category::where('user_id', auth()->id())->findOrFail($id);

        return ApiResponse::success($category);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $category = Category::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:income,expense,both',
        ]);

        $category->update($validated);

        return ApiResponse::success($category);
    }

    public function destroy(int $id): JsonResponse|Response
    {
        $category = Category::where('user_id', auth()->id())->findOrFail($id);

        if ($category->isUsed()) {
            $category->update(['is_archived' => true]);

            return ApiResponse::success(['archived' => true]);
        }

        $category->delete();

        return response()->noContent();
    }

    public function restore(int $id): JsonResponse
    {
        $category = Category::where('user_id', auth()->id())->findOrFail($id);
        $category->update(['is_archived' => false]);

        return ApiResponse::success($category);
    }
}
