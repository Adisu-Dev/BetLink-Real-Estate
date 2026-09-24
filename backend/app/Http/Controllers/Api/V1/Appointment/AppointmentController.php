<?php

namespace App\Http\Controllers\Api\V1\Appointment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\Property;
use App\Services\AppointmentService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    use ApiResponse;

    public function __construct(private AppointmentService $appointmentService) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $appointments = Appointment::with(['property.primaryImage', 'property.address.city', 'property.address.subCity', 'owner', 'visitor'])
            ->when(!$user->isAdmin(), fn($q) => $q->where(fn($sq) => $sq->where('visitor_id', $user->id)->orWhere('owner_id', $user->id)))
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->latest('scheduled_at')
            ->paginate($request->per_page ?? 15);

        return $this->paginated($appointments);
    }

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $property = Property::where('status', 'active')->findOrFail($request->property_id);

        if ($property->user_id === $request->user()->id) {
            return $this->error('You cannot book an appointment for your own property', 422);
        }

        // Prevent duplicate active viewing appointments for the same property
        $existing = Appointment::where('property_id', $property->id)
            ->where('visitor_id', $request->user()->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($existing) {
            $date = $existing->scheduled_at ? $existing->scheduled_at->format('M d, Y \a\t h:i A') : 'a scheduled time';
            return $this->error("You already have an active ({$existing->status}) viewing appointment for this property on {$date}. You can reschedule or cancel it from your appointments dashboard.", 422);
        }

        $appointment = $this->appointmentService->book($property, $request->user(), $request->validated());
        return $this->created($appointment, 'Appointment booked successfully');
    }

    public function show(Appointment $appointment): JsonResponse
    {
        $this->authorize('view', $appointment);
        return $this->success($appointment->load(['property.primaryImage', 'property.address.city', 'property.address.subCity', 'owner', 'visitor']));
    }

    public function confirm(Appointment $appointment, Request $request): JsonResponse
    {
        $this->authorize('manage', $appointment);

        if (!$appointment->isPending()) {
            return $this->error('Only pending appointments can be confirmed');
        }

        $appointment = $this->appointmentService->confirm($appointment, $request->user());
        return $this->success($appointment, 'Appointment confirmed');
    }

    public function cancel(Request $request, Appointment $appointment): JsonResponse
    {
        $this->authorize('cancel', $appointment);

        $request->validate(['reason' => ['nullable', 'string', 'max:500']]);

        if ($appointment->isCancelled()) {
            return $this->error('Appointment is already cancelled');
        }

        $appointment = $this->appointmentService->cancel($appointment, $request->user(), $request->reason);
        return $this->success($appointment, 'Appointment cancelled');
    }

    public function complete(Appointment $appointment): JsonResponse
    {
        $this->authorize('manage', $appointment);

        if (!$appointment->isConfirmed()) {
            return $this->error('Only confirmed appointments can be completed');
        }

        $appointment = $this->appointmentService->complete($appointment);
        return $this->success($appointment, 'Appointment marked as completed');
    }

    public function update(Request $request, Appointment $appointment): JsonResponse
    {
        $user = $request->user();
        if ($appointment->visitor_id !== $user->id && $appointment->owner_id !== $user->id && !method_exists($user, 'isAdmin') || ($user->isAdmin() === false && $appointment->visitor_id !== $user->id && $appointment->owner_id !== $user->id)) {
            // Check ownership
            if ($appointment->visitor_id !== $user->id && $appointment->owner_id !== $user->id) {
                return $this->error('Unauthorized to edit this appointment', 403);
            }
        }

        $validated = $request->validate([
            'scheduled_at'     => ['nullable', 'date'],
            'message'          => ['nullable', 'string', 'max:1000'],
            'reason'           => ['nullable', 'string', 'max:1000'],
            'type'             => ['nullable', 'string'],
            'duration_minutes' => ['nullable', 'integer', 'min:15', 'max:180'],
        ]);

        if (isset($validated['scheduled_at'])) {
            $appointment->scheduled_at = $validated['scheduled_at'];
            $appointment->status = 'pending';
        }
        $reasonText = $validated['reason'] ?? $validated['message'] ?? null;
        if ($reasonText) {
            $appointment->message = $reasonText;
            if ($user->id === $appointment->owner_id) {
                $appointment->owner_notes = $reasonText;
            }
        }
        if (isset($validated['type'])) {
            $appointment->type = $validated['type'];
        }
        if (isset($validated['duration_minutes'])) {
            $appointment->duration_minutes = $validated['duration_minutes'];
        }

        $appointment->save();

        return $this->success($appointment->load(['property.primaryImage', 'owner', 'visitor']), 'Appointment updated successfully');
    }

    public function destroy(Request $request, Appointment $appointment): JsonResponse
    {
        $user = $request->user();
        $isAdmin = method_exists($user, 'hasRole') ? $user->hasRole('admin') : (method_exists($user, 'isAdmin') ? $user->isAdmin() : false);
        if ($appointment->visitor_id !== $user->id && $appointment->owner_id !== $user->id && !$isAdmin) {
            return $this->error('Unauthorized to delete this appointment', 403);
        }

        $appointment->delete();

        return $this->success(null, 'Appointment deleted successfully');
    }

    public function ownerAppointments(Request $request): JsonResponse
    {
        $appointments = Appointment::with(['property.primaryImage', 'visitor'])
            ->where('owner_id', $request->user()->id)
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->latest('scheduled_at')
            ->paginate($request->per_page ?? 15);

        return $this->paginated($appointments);
    }

    public function calendar(Request $request): JsonResponse
    {
        $appointments = Appointment::where('owner_id', $request->user()->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->when($request->month, fn($q, $v) => $q->whereMonth('scheduled_at', $v))
            ->when($request->year, fn($q, $v) => $q->whereYear('scheduled_at', $v))
            ->with(['property:id,title,slug', 'visitor:id,name,avatar'])
            ->get();

        return $this->success($appointments);
    }
}
