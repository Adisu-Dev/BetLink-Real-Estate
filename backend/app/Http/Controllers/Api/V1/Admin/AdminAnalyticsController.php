<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminAnalyticsController extends Controller
{
    use ApiResponse;

    public function __construct(private AnalyticsService $analyticsService) {}

    public function dashboard(): JsonResponse
    {
        return $this->success($this->analyticsService->adminDashboard());
    }

    public function properties(Request $request): JsonResponse
    {
        $data = $this->analyticsService->propertyViews(
            $request->property_id ?? 0,
            $request->period ?? '30days'
        );
        return $this->success($data);
    }

    public function users(): JsonResponse
    {
        $data = [
            'by_role'   => \DB::table('model_has_roles')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->select('roles.name', \DB::raw('count(*) as total'))
                ->groupBy('roles.name')->get(),
            'by_month'  => \App\Models\User::selectRaw('YEAR(created_at) year, MONTH(created_at) month, count(*) total')
                ->groupBy('year', 'month')->orderBy('year')->orderBy('month')
                ->take(12)->get(),
        ];
        return $this->success($data);
    }
}
