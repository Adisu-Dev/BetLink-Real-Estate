<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Notifications\AppointmentReminder;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    protected $signature   = 'appointments:reminders';
    protected $description = 'Send reminders for appointments scheduled tomorrow';

    public function handle(): void
    {
        $tomorrow = now()->addDay();

        $appointments = Appointment::with(['owner', 'visitor', 'property'])
            ->whereIn('status', ['confirmed', 'pending'])
            ->whereBetween('scheduled_at', [
                $tomorrow->copy()->startOfDay(),
                $tomorrow->copy()->endOfDay(),
            ])
            ->where('reminder_sent', false)
            ->get();

        foreach ($appointments as $appointment) {
            $appointment->owner->notify(new AppointmentReminder($appointment));
            $appointment->visitor->notify(new AppointmentReminder($appointment));
            $appointment->update(['reminder_sent' => true]);
        }

        $this->info("Sent reminders for {$appointments->count()} appointments.");
    }
}
