<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function view(User $user, Appointment $appointment): bool
    {
        return in_array($user->id, [$appointment->owner_id, $appointment->visitor_id]) || $user->id === $appointment->property?->user_id || $user->isAdmin();
    }

    public function manage(User $user, Appointment $appointment): bool
    {
        return $user->id === $appointment->owner_id || $user->id === $appointment->property?->user_id || $user->isAdmin();
    }

    public function cancel(User $user, Appointment $appointment): bool
    {
        return in_array($user->id, [$appointment->owner_id, $appointment->visitor_id]) || $user->id === $appointment->property?->user_id || $user->isAdmin();
    }
}
