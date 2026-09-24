<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Property;
use App\Models\User;
use App\Notifications\AppointmentConfirmed;
use App\Notifications\AppointmentReminder;

class AppointmentService
{
    public function book(Property $property, User $visitor, array $data): Appointment
    {
        $appointment = Appointment::create([
            'property_id'      => $property->id,
            'owner_id'         => $property->user_id,
            'visitor_id'       => $visitor->id,
            'scheduled_at'     => $data['scheduled_at'],
            'duration_minutes' => $data['duration_minutes'] ?? 30,
            'type'             => $data['type'] ?? 'in_person',
            'message'          => $data['message'] ?? null,
            'status'           => 'pending',
        ]);

        $property->owner->notify(new \App\Notifications\AppointmentConfirmed($appointment));

        return $appointment->load(['property', 'visitor', 'owner']);
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
