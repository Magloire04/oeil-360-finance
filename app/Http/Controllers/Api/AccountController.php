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
        $accounts = Account::orderBy('name')->get();
        $data = $accounts->map(fn($account) => array_merge(
            $account->toArray(),
            ['balance' => $this->balanceService->getBalance($account)]
        ));
        return ApiResponse::success($data);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'type'            => 'required|in:cash,mobile_money,bank',
            'initial_balance' => 'sometimes|numeric|min:0',
        ]);
        $account = Account::create($validated);
        $data = array_merge($account->toArray(), ['balance' => $this->balanceService->getBalance($account)]);
        return ApiResponse::success($data, null, 201);
    }

    public function show(Account $account): JsonResponse
    {
        $data = array_merge($account->toArray(), ['balance' => $this->balanceService->getBalance($account)]);
        return ApiResponse::success($data);
    }

    public function update(Request $request, Account $account): JsonResponse
    {
        $validated = $request->validate([
            'name'            => 'sometimes|string|max:255',
            'type'            => 'sometimes|in:cash,mobile_money,bank',
            'initial_balance' => 'sometimes|numeric|min:0',
        ]);
        $account->update($validated);
        $data = array_merge($account->toArray(), ['balance' => $this->balanceService->getBalance($account)]);
        return ApiResponse::success($data);
    }

    public function destroy(Account $account): JsonResponse|Response
    {
        if ($account->isUsed()) {
            $account->update(['is_archived' => true]);
            return ApiResponse::success(['archived' => true]);
        }
        $account->delete();
        return response()->noContent();
    }

    public function restore(Account $account): JsonResponse
    {
        $account->update(['is_archived' => false]);
        $data = array_merge($account->toArray(), ['balance' => $this->balanceService->getBalance($account)]);
        return ApiResponse::success($data);
    }
}
