<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Property;
use App\Models\SellerAvailability;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OwnerAvailabilityController extends Controller
{
    use ApiResponse;

    /**
     * Get owner's working schedule and slot configurations
     */
    public function getAvailability(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $availabilities = SellerAvailability::where('user_id', $userId)
            ->orderBy('day_of_week')
            ->get();

        if ($availabilities->isEmpty()) {
            // Provide sensible defaults: Mon(1) - Sat(6) 09:00 to 17:00, Sun(0) off
            $defaults = [];
            for ($d = 0; $d <= 6; $d++) {
                $defaults[] = [
                    'day_of_week'           => $d,
                    'start_time'            => '09:00',
                    'end_time'              => '17:00',
                    'slot_duration_minutes' => 30,
                    'is_active'             => $d !== 0, // Sunday off
                ];
            }
            return $this->success($defaults, 'Default availability returned');
        }

        return $this->success($availabilities);
    }

    /**
     * Save/Update owner's working schedule
     */
    public function saveAvailability(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'schedules'                           => ['required', 'array'],
            'schedules.*.day_of_week'             => ['required', 'integer', 'min:0', 'max:6'],
            'schedules.*.start_time'              => ['required', 'string'],
            'schedules.*.end_time'                => ['required', 'string'],
            'schedules.*.slot_duration_minutes'   => ['required', 'integer', 'min:15', 'max:120'],
            'schedules.*.is_active'               => ['required', 'boolean'],
        ]);

        $userId = $request->user()->id;

        foreach ($validated['schedules'] as $sch) {
            SellerAvailability::updateOrCreate(
                [
                    'user_id'     => $userId,
                    'day_of_week' => $sch['day_of_week'],
                ],
                [
                    'start_time'            => substr($sch['start_time'], 0, 5) . ':00',
                    'end_time'              => substr($sch['end_time'], 0, 5) . ':00',
                    'slot_duration_minutes' => $sch['slot_duration_minutes'],
                    'is_active'             => $sch['is_active'],
                ]
            );
        }

        $fresh = SellerAvailability::where('user_id', $userId)->orderBy('day_of_week')->get();
        return $this->success($fresh, 'Availability schedule updated successfully');
    }

    /**
     * Get pre-approved bookable viewing slots for a property on a specific date
     */
    public function getAvailableSlots(Request $request, Property $property): JsonResponse
    {
        $dateStr = $request->query('date', Carbon::today()->format('Y-m-d'));
        
        try {
            $targetDate = Carbon::parse($dateStr)->startOfDay();
        } catch (\Exception $e) {
            $targetDate = Carbon::today()->startOfDay();
        }

        $dayOfWeek = $targetDate->dayOfWeek; // 0 = Sun, 6 = Sat
        $ownerId = $property->user_id;

        // Fetch owner availability for this day
        $schedule = SellerAvailability::where('user_id', $ownerId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        // If no schedule exists in DB, use default Mon-Sat 09:00 - 17:00
        $isActive = $schedule ? (bool)$schedule->is_active : ($dayOfWeek !== 0);
        $startTime = $schedule ? substr($schedule->start_time, 0, 5) : '09:00';
        $endTime = $schedule ? substr($schedule->end_time, 0, 5) : '17:00';
        $duration = $schedule ? (int)$schedule->slot_duration_minutes : 30;

        if (!$isActive) {
            return $this->success([
                'date'             => $targetDate->format('Y-m-d'),
                'is_working_day'   => false,
                'slots'            => [],
                'message'          => 'The property owner is not available for viewings on this day of the week.',
            ]);
        }

        // Fetch already booked appointments for this property or owner on this day
        $existingAppointments = Appointment::where(function ($q) use ($property, $ownerId) {
                $q->where('property_id', $property->id)
                  ->orWhere('owner_id', $ownerId);
            })
            ->whereDate('scheduled_at', $targetDate->format('Y-m-d'))
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();

        $bookedTimes = $existingAppointments->map(function ($app) {
            return $app->scheduled_at ? $app->scheduled_at->format('H:i') : null;
        })->filter()->values()->toArray();

        // Generate discrete slots
        $slots = [];
        $start = Carbon::parse($targetDate->format('Y-m-d') . ' ' . $startTime);
        $end = Carbon::parse($targetDate->format('Y-m-d') . ' ' . $endTime);
        $now = Carbon::now();

        while ($start->copy()->addMinutes($duration)->lte($end)) {
            $timeSlotKey = $start->format('H:i');
            $displayTime = $start->format('g:i A');
            $fullDateTime = $start->format('Y-m-d H:i:s');
            
            // Check if slot has passed or is booked
            $isPast = $start->lte($now);
            $isBooked = in_array($timeSlotKey, $bookedTimes);
            $isAvailable = !$isPast && !$isBooked;

            $slots[] = [
                'time'          => $timeSlotKey,
                'display_time'  => $displayTime,
                'datetime'      => $fullDateTime,
                'available'     => $isAvailable,
                'reason'        => $isPast ? 'past' : ($isBooked ? 'booked' : 'open'),
            ];

            $start->addMinutes($duration);
        }

        return $this->success([
            'date'                   => $targetDate->format('Y-m-d'),
            'day_of_week'            => $dayOfWeek,
            'is_working_day'         => true,
            'slot_duration_minutes'  => $duration,
            'slots'                  => $slots,
        ]);
    }
}
