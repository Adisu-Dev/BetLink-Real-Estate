<?php

namespace App\Http\Controllers\Api\V1\Booking;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyAvailability;
use App\Models\ShortRentalBooking;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    use ApiResponse;

    public function availability(Property $property): JsonResponse
    {
        $availability = $property->availability()
            ->where('date', '>=', now()->toDateString())
            ->where('date', '<=', now()->addMonths(3)->toDateString())
            ->get();

        // Also get booked dates
        $bookedDates = $property->shortRentalBookings()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->where('check_out_date', '>=', now()->toDateString())
            ->get(['check_in_date', 'check_out_date']);

        return $this->success([
            'availability'  => $availability,
            'booked_dates'  => $bookedDates,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'property_id'    => ['required', 'exists:properties,id'],
            'check_in_date'  => ['required', 'date', 'after:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'guests_count'   => ['nullable', 'integer', 'min:1'],
            'special_requests' => ['nullable', 'string'],
        ]);

        $property = Property::where('status', 'active')
            ->where('listing_type', 'short_rent')
            ->findOrFail($request->property_id);

        $checkIn  = \Carbon\Carbon::parse($request->check_in_date);
        $checkOut = \Carbon\Carbon::parse($request->check_out_date);
        $nights   = $checkIn->diffInDays($checkOut);

        // Check for conflicts
        $conflict = ShortRentalBooking::where('property_id', $property->id)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->where('check_in_date', '<', $request->check_out_date)
            ->where('check_out_date', '>', $request->check_in_date)
            ->exists();

        if ($conflict) {
            return $this->error('The selected dates are not available', 409);
        }

        $pricePerNight = $property->price;
        $totalPrice    = $pricePerNight * $nights;
        $serviceFee    = round($totalPrice * 0.05, 2); // 5% service fee

        $booking = ShortRentalBooking::create([
            'property_id'      => $property->id,
            'guest_id'         => $request->user()->id,
            'check_in_date'    => $request->check_in_date,
            'check_out_date'   => $request->check_out_date,
            'nights'           => $nights,
            'guests_count'     => $request->guests_count ?? 1,
            'total_price'      => $totalPrice,
            'service_fee'      => $serviceFee,
            'special_requests' => $request->special_requests,
            'status'           => 'pending',
        ]);

        return $this->created($booking->load(['property', 'guest']), 'Booking created successfully');
    }

    public function index(Request $request): JsonResponse
    {
        $bookings = ShortRentalBooking::with(['property.primaryImage', 'property.address.city'])
            ->where('guest_id', $request->user()->id)
            ->latest()
            ->paginate($request->per_page ?? 15);

        return $this->paginated($bookings);
    }

    public function show(ShortRentalBooking $booking): JsonResponse
    {
        if ($booking->guest_id !== auth()->id()) {
            return $this->forbidden();
        }
        return $this->success($booking->load(['property', 'guest']));
    }

    public function cancel(Request $request, ShortRentalBooking $booking): JsonResponse
    {
        if ($booking->guest_id !== $request->user()->id) {
            return $this->forbidden();
        }
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return $this->error('This booking cannot be cancelled');
        }
        $booking->update(['status' => 'cancelled']);
        return $this->success($booking, 'Booking cancelled');
    }
}
