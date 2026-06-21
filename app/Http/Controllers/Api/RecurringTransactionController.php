<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\RecurringTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class RecurringTransactionController extends Controller
{
    public function index(): JsonResponse
    {
        $recurringTransactions = RecurringTransaction::with(['category', 'account'])
            ->where('user_id', auth()->id())
            ->orderBy('next_occurrence_date')
            ->get();

        return ApiResponse::success($recurringTransactions);
    }

    public function store(Request $request): JsonResponse
    {
        $userId    = (int) auth()->id();
        $validated = $request->validate([
            'amount'      => 'required|numeric|min:0.01',
            'sense'       => 'required|in:income,expense',
            'frequency'   => 'required|in:daily,weekly,monthly,yearly',
            'start_date'  => 'required|date',
            'category_id' => ['required', Rule::exists('categories', 'id')->where('user_id', $userId)],
            'account_id'  => ['required', Rule::exists('accounts', 'id')->where('user_id', $userId)],
            'note'        => 'nullable|string',
        ]);

        $validated['next_occurrence_date'] = $validated['start_date'];

        $recurring = RecurringTransaction::create(['user_id' => $userId] + $validated);
        $recurring->load(['category', 'account']);

        return ApiResponse::success($recurring, null, 201);
    }

    public function show(int $id): JsonResponse
    {
        $recurringTransaction = RecurringTransaction::with(['category', 'account'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return ApiResponse::success($recurringTransaction);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $userId               = (int) auth()->id();
        $recurringTransaction = RecurringTransaction::where('user_id', $userId)->findOrFail($id);

        $validated = $request->validate([
            'amount'      => 'sometimes|numeric|min:0.01',
            'sense'       => 'sometimes|in:income,expense',
            'frequency'   => 'sometimes|in:daily,weekly,monthly,yearly',
            'start_date'  => 'sometimes|date',
            'category_id' => ['sometimes', Rule::exists('categories', 'id')->where('user_id', $userId)],
            'account_id'  => ['sometimes', Rule::exists('accounts', 'id')->where('user_id', $userId)],
            'note'        => 'nullable|string',
            'is_active'   => 'sometimes|boolean',
        ]);

        $recurringTransaction->update($validated);
        $recurringTransaction->load(['category', 'account']);

        return ApiResponse::success($recurringTransaction);
    }

    public function destroy(int $id): Response
    {
        $recurringTransaction = RecurringTransaction::where('user_id', (int) auth()->id())->findOrFail($id);
        $recurringTransaction->delete();

        return response()->noContent();
    }
}
