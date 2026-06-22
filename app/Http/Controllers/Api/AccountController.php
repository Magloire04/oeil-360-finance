<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Account;
use App\Services\AccountBalanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccountController extends Controller
{
    public function __construct(private AccountBalanceService $balanceService) {}

    public function index(): JsonResponse
    {
        $accounts = Account::where('user_id', auth()->id())->orderBy('name')->get();
        $data = $accounts->map(fn (Account $account) => [
            ...$account->toArray(),
            'balance' => $this->balanceService->getBalance($account),
        ]);

        return ApiResponse::success($data);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:cash,mobile_money,bank',
            'initial_balance' => 'sometimes|numeric|min:0',
        ]);

        $account = Account::create(['user_id' => auth()->id()] + $validated);
        $data = array_merge($account->toArray(), ['balance' => $this->balanceService->getBalance($account)]);

        return ApiResponse::success($data, null, 201);
    }

    public function show(int $id): JsonResponse
    {
        $account = Account::where('user_id', auth()->id())->findOrFail($id);
        $data = array_merge($account->toArray(), ['balance' => $this->balanceService->getBalance($account)]);

        return ApiResponse::success($data);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $account = Account::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:cash,mobile_money,bank',
            'initial_balance' => 'sometimes|numeric|min:0',
        ]);

        $account->update($validated);
        $data = array_merge($account->toArray(), ['balance' => $this->balanceService->getBalance($account)]);

        return ApiResponse::success($data);
    }

    public function destroy(int $id): JsonResponse|Response
    {
        $account = Account::where('user_id', auth()->id())->findOrFail($id);

        if ($account->isUsed()) {
            $account->update(['is_archived' => true]);

            return ApiResponse::success(['archived' => true]);
        }

        $account->delete();

        return response()->noContent();
    }

    public function restore(int $id): JsonResponse
    {
        $account = Account::where('user_id', auth()->id())->findOrFail($id);
        $account->update(['is_archived' => false]);
        $data = array_merge($account->toArray(), ['balance' => $this->balanceService->getBalance($account)]);

        return ApiResponse::success($data);
    }
}
