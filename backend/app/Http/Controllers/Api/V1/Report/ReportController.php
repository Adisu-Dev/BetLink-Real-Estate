<?php

namespace App\Http\Controllers\Api\V1\Report;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use ApiResponse;

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'reportable_type' => ['required', 'in:property,user,review'],
            'reportable_id'   => ['required', 'integer'],
            'reason'          => ['required', 'in:spam,fraud,inappropriate,duplicate,other'],
            'description'     => ['nullable', 'string', 'max:1000'],
        ]);

        $typeMap = [
            'property' => \App\Models\Property::class,
            'user'     => \App\Models\User::class,
            'review'   => \App\Models\Review::class,
        ];

        // Prevent duplicate reports
        $exists = Report::where('reportable_type', $typeMap[$request->reportable_type])
            ->where('reportable_id', $request->reportable_id)
            ->where('reporter_id', $request->user()->id)
            ->exists();

        if ($exists) {
            return $this->error('You have already reported this', 409);
        }

        Report::create([
            'reportable_type' => $typeMap[$request->reportable_type],
            'reportable_id'   => $request->reportable_id,
            'reporter_id'     => $request->user()->id,
            'reason'          => $request->reason,
            'description'     => $request->description,
        ]);

        return $this->created(null, 'Report submitted. Thank you for keeping BetLink safe.');
    }
}
