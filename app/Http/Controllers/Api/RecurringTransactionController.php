<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\RecurringTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RecurringTransactionController extends Controller
{
    public function index(): JsonResponse
    {
        $recurringTransactions = RecurringTransaction::with(['category', 'account'])
            ->orderBy('next_occurrence_date')
            ->get();

        return ApiResponse::success($recurringTransactions);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount'      => 'required|numeric|min:0.01',
            'sense'       => 'required|in:income,expense',
            'frequency'   => 'required|in:daily,weekly,monthly,yearly',
            'start_date'  => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'account_id'  => 'required|exists:accounts,id',
            'note'        => 'nullable|string',
        ]);

        // next_occurrence_date est initialisé à start_date
        $validated['next_occurrence_date'] = $validated['start_date'];

        $recurring = RecurringTransaction::create($validated);
        $recurring->load(['category', 'account']);

        return ApiResponse::success($recurring, null, 201);
    }

    public function show(RecurringTransaction $recurringTransaction): JsonResponse
    {
        $recurringTransaction->load(['category', 'account']);
        return ApiResponse::success($recurringTransaction);
    }

    public function update(Request $request, RecurringTransaction $recurringTransaction): JsonResponse
    {
        $validated = $request->validate([
            'amount'      => 'sometimes|numeric|min:0.01',
            'sense'       => 'sometimes|in:income,expense',
            'frequency'   => 'sometimes|in:daily,weekly,monthly,yearly',
            'start_date'  => 'sometimes|date',
            'category_id' => 'sometimes|exists:categories,id',
            'account_id'  => 'sometimes|exists:accounts,id',
            'note'        => 'nullable|string',
            'is_active'   => 'sometimes|boolean',
        ]);

        $recurringTransaction->update($validated);
        $recurringTransaction->load(['category', 'account']);

        return ApiResponse::success($recurringTransaction);
    }

    public function destroy(RecurringTransaction $recurringTransaction): Response
    {
        // Les transactions déjà générées restent : FK nullOnDelete s'en charge automatiquement
        $recurringTransaction->delete();
        return response()->noContent();
    }
}
