<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Transfer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransferController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Transfer::with(['fromAccount', 'toAccount'])
            ->orderBy('transfer_date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->filled('account_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('from_account_id', $request->account_id)
                  ->orWhere('to_account_id', $request->account_id);
            });
        }
        if ($request->filled('start_date')) {
            $query->where('transfer_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('transfer_date', '<=', $request->end_date);
        }

        return ApiResponse::success($query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount'          => 'required|numeric|min:0.01',
            'transfer_date'   => 'required|date',
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id'   => 'required|exists:accounts,id|different:from_account_id',
            'note'            => 'nullable|string',
        ]);

        $transfer = Transfer::create($validated);
        $transfer->load(['fromAccount', 'toAccount']);

        return ApiResponse::success($transfer, null, 201);
    }

    public function show(Transfer $transfer): JsonResponse
    {
        $transfer->load(['fromAccount', 'toAccount']);
        return ApiResponse::success($transfer);
    }

    public function update(Request $request, Transfer $transfer): JsonResponse
    {
        $validated = $request->validate([
            'amount'          => 'sometimes|numeric|min:0.01',
            'transfer_date'   => 'sometimes|date',
            'from_account_id' => 'sometimes|exists:accounts,id',
            'to_account_id'   => 'sometimes|exists:accounts,id|different:from_account_id',
            'note'            => 'nullable|string',
        ]);

        $transfer->update($validated);
        $transfer->load(['fromAccount', 'toAccount']);

        return ApiResponse::success($transfer);
    }

    public function destroy(Transfer $transfer): Response
    {
        $transfer->delete();
        return response()->noContent();
    }
}
