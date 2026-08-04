<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\AdminMetricsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MetricsController extends Controller
{
    public function __construct(private AdminMetricsService $metrics) {}

    public function overview(Request $request): JsonResponse
    {
        [$start, $end] = $this->range($request);

        return ApiResponse::success($this->metrics->getOverview($start, $end));
    }

    public function userGrowth(): JsonResponse
    {
        return ApiResponse::success($this->metrics->getUserGrowth());
    }

    public function activeUsers(Request $request): JsonResponse
    {
        [$start, $end] = $this->range($request);

        return ApiResponse::success($this->metrics->getActiveUsers($start, $end));
    }

    public function operations(Request $request): JsonResponse
    {
        [$start, $end] = $this->range($request);

        return ApiResponse::success($this->metrics->getOperationsBreakdown($start, $end));
    }

    public function topFeatures(Request $request): JsonResponse
    {
        [$start, $end] = $this->range($request);

        return ApiResponse::success($this->metrics->getTopFeatures($start, $end));
    }

    public function traffic(Request $request): JsonResponse
    {
        [$start, $end] = $this->range($request);

        return ApiResponse::success($this->metrics->getTraffic($start, $end));
    }

    public function performance(Request $request): JsonResponse
    {
        [$start, $end] = $this->range($request);

        return ApiResponse::success($this->metrics->getPerformance($start, $end));
    }

    /** @return array{0: string, 1: string} */
    private function range(Request $request): array
    {
        return [
            $request->input('start_date', now()->subDays(29)->toDateString()),
            $request->input('end_date', now()->toDateString()),
        ];
    }
}
