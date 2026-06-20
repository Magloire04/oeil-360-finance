<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransactionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Transaction::with(['category', 'account'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->filled('start_date')) {
            $query->where('transaction_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('transaction_date', '<=', $request->end_date);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }
        if ($request->filled('sense')) {
            $query->where('sense', $request->sense);
        }
        if ($request->filled('q')) {
            $query->where('note', 'like', '%' . $request->q . '%');
        }

        $perPage = (int) $request->get('per_page', 25);
        $transactions = $query->paginate($perPage);

        return ApiResponse::success($transactions->items(), [
            'total'        => $transactions->total(),
            'per_page'     => $transactions->perPage(),
            'current_page' => $transactions->currentPage(),
            'last_page'    => $transactions->lastPage(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount'           => 'required|numeric|min:0.01',
            'sense'            => 'required|in:income,expense',
            'transaction_date' => 'required|date',
            'category_id'      => 'required|exists:categories,id',
            'account_id'       => 'required|exists:accounts,id',
            'note'             => 'nullable|string',
        ]);

        $transaction = Transaction::create($validated);
        $transaction->load(['category', 'account']);

        return ApiResponse::success($transaction, null, 201);
    }

    public function show(Transaction $transaction): JsonResponse
    {
        $transaction->load(['category', 'account']);

        return ApiResponse::success($transaction);
    }

    public function update(Request $request, Transaction $transaction): JsonResponse
    {
        $validated = $request->validate([
            'amount'           => 'sometimes|numeric|min:0.01',
            'sense'            => 'sometimes|in:income,expense',
            'transaction_date' => 'sometimes|date',
            'category_id'      => 'sometimes|exists:categories,id',
            'account_id'       => 'sometimes|exists:accounts,id',
            'note'             => 'nullable|string',
        ]);

        $transaction->update($validated);
        $transaction->load(['category', 'account']);

        return ApiResponse::success($transaction);
    }

    public function destroy(Transaction $transaction): Response
    {
        $transaction->delete();

        return response()->noContent();
    }
}
