<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}

    public function index(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        $summary = $this->dashboardService->getSummary($startDate, $endDate, (int) auth()->id());

        return ApiResponse::success($summary);
    }

    public function monthly(): JsonResponse
    {
        $data = $this->dashboardService->getMonthlyComparison((int) auth()->id());

        return ApiResponse::success($data);
    }
}
