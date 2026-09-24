<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Property;
use App\Models\User;
use App\Notifications\AppointmentConfirmed;
use App\Notifications\AppointmentReminder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AppointmentService
{
    /**
     * Atomically book an appointment with pessimistic concurrency locking.
     * Prevents race conditions and double-booking collisions between simultaneous visitors.
     */
    public function book(Property $property, User $visitor, array $data): Appointment
    {
        return DB::transaction(function () use ($property, $visitor, $data) {
            $scheduledAt = Carbon::parse($data['scheduled_at']);
            $duration = (int)($data['duration_minutes'] ?? 30);
            $slotEnd = (clone $scheduledAt)->addMinutes($duration);

            // Step 3 & 4: Concurrency Lock & Collision Check
            // Pessimistically lock conflicting appointment records for this property
            $hasConflict = Appointment::where('property_id', $property->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where(function ($query) use ($scheduledAt, $slotEnd) {
                    $query->whereBetween('scheduled_at', [$scheduledAt, $slotEnd])
                          ->orWhere(function ($q) use ($scheduledAt) {
                              $q->where('scheduled_at', '<=', $scheduledAt)
                                ->whereRaw('DATE_ADD(scheduled_at, INTERVAL duration_minutes MINUTE) > ?', [$scheduledAt]);
                          });
                })
                ->lockForUpdate()
                ->exists();

            if ($hasConflict) {
                throw ValidationException::withMessages([
                    'scheduled_at' => ['ይህ የቀጠሮ ሰዓት ቀድሞ በሌላ ጎብኚ ተይዟል። እባክዎ ሌላ የተመቸ ሰዓት ይምረጡ (This inspection time slot is already reserved by another visitor. Please select a different time slot).']
                ]);
            }

            $appointment = Appointment::create([
                'property_id'      => $property->id,
                'owner_id'         => $property->user_id,
                'visitor_id'       => $visitor->id,
                'scheduled_at'     => $scheduledAt,
                'duration_minutes' => $duration,
                'type'             => $data['type'] ?? 'in_person',
                'message'          => $data['message'] ?? null,
                'status'           => 'pending',
            ]);

            try {
                $property->owner?->notify(new \App\Notifications\AppointmentConfirmed($appointment));
            } catch (\Throwable $e) {
                Log::warning("Appointment notification failed: " . $e->getMessage());
            }

            return $appointment->load(['property', 'visitor', 'owner']);
        });
    }

    public function confirm(Appointment $appointment, User $owner): Appointment
    {
        $appointment->update(['status' => 'confirmed']);
        $appointment->visitor->notify(new \App\Notifications\AppointmentConfirmed($appointment));
        return $appointment;
    }

    public function cancel(Appointment $appointment, User $canceller, string $reason = null): Appointment
    {
        $appointment->update([
            'status'              => 'cancelled',
            'cancelled_by'        => $canceller->id,
            'cancellation_reason' => $reason,
        ]);
        return $appointment;
    }

    public function complete(Appointment $appointment): Appointment
    {
        $appointment->update(['status' => 'completed']);
        return $appointment;
    }
}
