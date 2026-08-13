<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Availability;
use App\Models\BlockedTime;
use App\Models\Booking;
use Carbon\Carbon;

class AvailabilityService
{
    /**
     * Generate available slots for a specific service on a given date.
     *
     * @param int $serviceId
     * @param string $dateStr (YYYY-MM-DD)
     * @return array List of available time slots (e.g., ["14:00", "14:30", ...])
     */
    public function getAvailableSlots(int $serviceId, string $dateStr): array
    {
        $service = Service::find($serviceId);
        if (!$service || !$service->is_active) {
            return [];
        }

        $date = Carbon::parse($dateStr);
        $today = Carbon::today();

        // 1. Prevent booking in the past
        if ($date->lt($today)) {
            return [];
        }

        // 2. Check if the day is completely blocked
        $blockedDay = BlockedTime::where('date', $dateStr)
            ->whereNull('start_time')
            ->first();
        if ($blockedDay) {
            return [];
        }

        // 3. Get the doctor's general availability for this day of the week
        // 0 = Sunday, 1 = Monday, etc.
        $dayOfWeek = $date->dayOfWeek;
        $generalAvailability = Availability::where('day_of_week', $dayOfWeek)->first();
        if (!$generalAvailability) {
            return [];
        }

        // Parse work hours
        $workStart = Carbon::parse($generalAvailability->start_time);
        $workEnd = Carbon::parse($generalAvailability->end_time);

        // 4. Get partial blocked times for this date
        $partialBlocks = BlockedTime::where('date', $dateStr)
            ->whereNotNull('start_time')
            ->whereNotNull('end_time')
            ->get();

        // 5. Get existing active bookings for this date
        // Active statuses: AwaitingPayment, Confirmed, Completed
        $existingBookings = Booking::where('date', $dateStr)
            ->whereIn('status', ['AwaitingPayment', 'Confirmed', 'Completed'])
            ->get();

        $duration = $service->duration;
        $slots = [];
        $currentSlot = $workStart->copy();

        // Generate slots
        while ($currentSlot->copy()->addMinutes($duration)->lte($workEnd)) {
            $slotStart = $currentSlot->copy();
            $slotEnd = $currentSlot->copy()->addMinutes($duration);

            $slotStartStr = $slotStart->format('H:i');
            $slotEndStr = $slotEnd->format('H:i');

            // A. If the date is TODAY, the slot start time must be in the future
            if ($date->isToday()) {
                $now = Carbon::now();
                // Compare with current time
                $slotDateTime = Carbon::parse($dateStr . ' ' . $slotStartStr);
                if ($slotDateTime->lte($now)) {
                    $currentSlot->addMinutes(15); // Slide by 15 mins (or by duration, let's slide by 15 minutes for denser/better options, or by service duration. Let's slide by 15 mins for flexible scheduling!)
                    continue;
                }
            }

            // B. Check if slot overlaps with any partial block
            $isBlocked = false;
            foreach ($partialBlocks as $block) {
                $blockStart = Carbon::parse($block->start_time);
                $blockEnd = Carbon::parse($block->end_time);

                // Overlap condition: slotStart < blockEnd && slotEnd > blockStart
                if ($slotStart->lt($blockEnd) && $slotEnd->gt($blockStart)) {
                    $isBlocked = true;
                    break;
                }
            }

            if ($isBlocked) {
                $currentSlot->addMinutes(15);
                continue;
            }

            // C. Check if slot overlaps with any existing booking
            $isBooked = false;
            foreach ($existingBookings as $booking) {
                $bookingStart = Carbon::parse($booking->start_time);
                $bookingEnd = Carbon::parse($booking->end_time);

                // Overlap condition: slotStart < bookingEnd && slotEnd > bookingStart
                if ($slotStart->lt($bookingEnd) && $slotEnd->gt($bookingStart)) {
                    $isBooked = true;
                    break;
                }
            }

            if (!$isBooked) {
                $slots[] = [
                    'start' => $slotStartStr,
                    'end' => $slotEndStr,
                ];
            }

            // Slide the window by 15 minutes to allow flexible booking slots, or by service duration.
            // Using 15 or 30 minutes increments is standard. Let's use 30 minutes or $duration.
            // Sliding by 30 minutes or $duration avoids overlap within the generated list.
            // If we slide by 15 minutes, the client can choose 14:00 or 14:15. Once they choose 14:00, 14:15 becomes unavailable due to overlap.
            // This is actually extremely premium and dynamic! Let's slide by 15 minutes so the user has maximum choice.
            $currentSlot->addMinutes(15);
        }

        return $slots;
    }
}
