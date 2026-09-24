<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $reports = Report::with(['reporter:id,name,email', 'reportable'])
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->reason, fn($q, $v) => $q->where('reason', $v))
            ->latest()
            ->paginate($request->per_page ?? 20);

        return $this->paginated($reports);
    }

    public function update(Request $request, Report $report): JsonResponse
    {
        $request->validate([
            'status'      => ['required', 'in:reviewed,resolved,dismissed'],
            'admin_notes' => ['nullable', 'string'],
        ]);

        $report->update([
            'status'      => $request->status,
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return $this->success($report, 'Report updated');
    }
}
