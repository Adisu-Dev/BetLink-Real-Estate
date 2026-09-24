<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OwnerAppointmentController extends Controller
{
    use ApiResponse;

    /**
     * Get owner tour requests with status filtering
     */
    public function index(Request $request): JsonResponse
    {
        $ownerId = $request->user()->id;

        $query = Appointment::where('owner_id', $ownerId)
            ->with(['property.primaryImage', 'property.address.city', 'property.address.subCity', 'visitor'])
            ->latest('scheduled_at');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $perPage = $request->per_page ?? 15;
        $appointments = $query->paginate($perPage);

        $transformed = $appointments->through(function ($appt) {
            return [
                'id'               => $appt->id,
                'property_id'      => $appt->property_id,
                'property_title'   => $appt->property?->title ?? 'Property Viewing Tour',
                'property_image'   => $appt->property?->primaryImage?->url ?? 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=600&q=80',
                'visitor_id'       => $appt->visitor_id,
                'visitor_name'     => $appt->visitor?->name ?? 'Interested Buyer',
                'visitor_email'    => $appt->visitor?->email,
                'visitor_phone'    => $appt->visitor?->phone ?? '+251 91 123 4567',
                'visitor_avatar'   => $appt->visitor?->avatar_url,
                'scheduled_at'     => $appt->scheduled_at?->toIso8601String() ?? (string) $appt->scheduled_at,
                'status'           => $appt->status,
                'type'             => $appt->type ?? 'in_person',
                'message'          => $appt->message,
                'duration_minutes' => $appt->duration_minutes ?? 30,
                'created_at'       => $appt->created_at?->toIso8601String(),
            ];
        });

        return $this->paginated($transformed);
    }

    /**
     * Approve tour request
     */
    public function approve(Request $request, Appointment $appointment): JsonResponse
    {
        if ($appointment->owner_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return $this->error('Unauthorized', 403);
        }

        $appointment->status = 'confirmed';
        $appointment->save();

        return $this->success($appointment, 'Appointment confirmed successfully');
    }

    /**
     * Reject tour request
     */
    public function reject(Request $request, Appointment $appointment): JsonResponse
    {
        if ($appointment->owner_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return $this->error('Unauthorized', 403);
        }

        $appointment->status = 'cancelled';
        $appointment->cancellation_reason = $request->reason ?? 'Declined by property owner';
        $appointment->save();

        return $this->success($appointment, 'Appointment rejected');
    }

    /**
     * Mark tour as completed
     */
    public function complete(Request $request, Appointment $appointment): JsonResponse
    {
        if ($appointment->owner_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return $this->error('Unauthorized', 403);
        }

        $appointment->status = 'completed';
        $appointment->save();

        return $this->success($appointment, 'Appointment marked as completed');
    }
}
