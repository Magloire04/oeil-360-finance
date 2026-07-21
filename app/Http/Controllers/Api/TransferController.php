<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Transfer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class TransferController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Transfer::with(['fromAccount', 'toAccount'])
            ->where('user_id', auth()->id())
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
        $userId = (int) auth()->id();
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'transfer_date' => 'required|date',
            'from_account_id' => ['required', Rule::exists('accounts', 'id')->where('user_id', $userId)],
            'to_account_id' => ['required', 'different:from_account_id', Rule::exists('accounts', 'id')->where('user_id', $userId)],
            'note' => 'nullable|string',
        ]);

        $transfer = Transfer::create(['user_id' => $userId] + $validated);
        $transfer->load(['fromAccount', 'toAccount']);

        return ApiResponse::success($transfer, null, 201);
    }

    public function show(int $id): JsonResponse
    {
        $transfer = Transfer::with(['fromAccount', 'toAccount'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return ApiResponse::success($transfer);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $userId = (int) auth()->id();
        $transfer = Transfer::where('user_id', $userId)->findOrFail($id);

        $validated = $request->validate([
            'amount' => 'sometimes|numeric|min:0.01',
            'transfer_date' => 'sometimes|date',
            'from_account_id' => ['sometimes', Rule::exists('accounts', 'id')->where('user_id', $userId)],
            'to_account_id' => ['sometimes', 'different:from_account_id', Rule::exists('accounts', 'id')->where('user_id', $userId)],
            'note' => 'nullable|string',
        ]);

        $transfer->update($validated);
        $transfer->load(['fromAccount', 'toAccount']);

        return ApiResponse::success($transfer);
    }

    public function destroy(int $id): Response
    {
        $transfer = Transfer::where('user_id', (int) auth()->id())->findOrFail($id);
        $transfer->delete();

        return response()->noContent();
    }
}
