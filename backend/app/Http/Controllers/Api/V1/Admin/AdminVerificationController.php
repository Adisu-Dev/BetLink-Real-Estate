<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\VerificationRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminVerificationController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $requests = VerificationRequest::with(['user:id,name,email,avatar'])
            ->when($request->search, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('notes', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->type, fn($q, $v) => $q->where('type', $v))
            ->latest()
            ->paginate($request->per_page ?? 20);

        return $this->paginated($requests);
    }

    public function approve(Request $request, VerificationRequest $verification): JsonResponse
    {
        $request->validate(['notes' => ['nullable', 'string']]);

        $verification->update([
            'status'      => 'approved',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'notes'       => $request->notes,
        ]);

        // Mark user profile as verified
        $verification->user->profile()->updateOrCreate(
            ['user_id' => $verification->user_id],
            ['is_verified' => true, 'verified_at' => now()]
        );

        if (!$verification->user->email_verified_at) {
            $verification->user->update(['email_verified_at' => now()]);
        }

        return $this->success($verification, 'Verification approved successfully');
    }

    public function reject(Request $request, VerificationRequest $verification): JsonResponse
    {
        $request->validate(['notes' => ['required', 'string']]);

        $verification->update([
            'status'      => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'notes'       => $request->notes,
        ]);

        return $this->success($verification, 'Verification rejected');
    }
}
