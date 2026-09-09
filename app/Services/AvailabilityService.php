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

        try {
            $date = Carbon::parse($dateStr);
            $dateStr = $date->format('Y-m-d');
        } catch (\Exception $e) {
            $date = Carbon::today();
            $dateStr = $date->format('Y-m-d');
        }
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
        
        $workStart = $generalAvailability ? Carbon::parse($generalAvailability->start_time) : Carbon::parse('09:00');
        $workEnd = $generalAvailability ? Carbon::parse($generalAvailability->end_time) : Carbon::parse('23:00');

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

            $currentSlot->addMinutes(15);
        }

        return $slots;
    }

    /**
     * Generate full slots schedule with clear separation of Available and Booked/Unavailable slots.
     *
     * @param int $serviceId
     * @param string $dateStr (YYYY-MM-DD)
     * @return array
     */
    public function getSlotsDetailed(int $serviceId, string $dateStr): array
    {
        $service = Service::find($serviceId);
        if (!$service || !$service->is_active) {
            return [
                'date' => $dateStr,
                'is_day_available' => false,
                'available_slots' => [],
                'unavailable_slots' => [],
                'all_slots' => [],
            ];
        }

        try {
            $date = Carbon::parse($dateStr);
            $dateStr = $date->format('Y-m-d');
        } catch (\Exception $e) {
            $date = Carbon::today();
            $dateStr = $date->format('Y-m-d');
        }
        $today = Carbon::today();

        if ($date->lt($today)) {
            return [
                'date' => $dateStr,
                'is_day_available' => false,
                'message' => 'التاريخ في الماضي',
                'available_slots' => [],
                'unavailable_slots' => [],
                'all_slots' => [],
            ];
        }

        // Full day blocked check
        $blockedDay = BlockedTime::where('date', $dateStr)
            ->whereNull('start_time')
            ->first();
        if ($blockedDay) {
            return [
                'date' => $dateStr,
                'is_day_available' => false,
                'message' => 'اليوم محظور بالكامل من الطبيب',
                'available_slots' => [],
                'unavailable_slots' => [],
                'all_slots' => [],
            ];
        }

        $dayOfWeek = $date->dayOfWeek;
        $generalAvailability = Availability::where('day_of_week', $dayOfWeek)->first();

        $workStart = $generalAvailability ? Carbon::parse($generalAvailability->start_time) : Carbon::parse('09:00');
        $workEnd = $generalAvailability ? Carbon::parse($generalAvailability->end_time) : Carbon::parse('23:00');

        $partialBlocks = BlockedTime::where('date', $dateStr)
            ->whereNotNull('start_time')
            ->whereNotNull('end_time')
            ->get();

        $existingBookings = Booking::where('date', $dateStr)
            ->whereIn('status', ['AwaitingPayment', 'Confirmed', 'Completed'])
            ->get();

        $duration = max((int)$service->duration, 15);
        $availableSlots = [];
        $unavailableSlots = [];
        $allSlots = [];

        $currentSlot = $workStart->copy();
        $now = Carbon::now();

        while ($currentSlot->copy()->addMinutes($duration)->lte($workEnd)) {
            $slotStart = $currentSlot->copy();
            $slotEnd = $currentSlot->copy()->addMinutes($duration);

            $slotStartStr = $slotStart->format('H:i');
            $slotEndStr = $slotEnd->format('H:i');
            $slotDateTime = Carbon::parse($dateStr . ' ' . $slotStartStr);

            $isAvailable = true;
            $status = 'available';
            $statusLabel = 'متاح للحجز';

            // 1. Past check
            if ($date->isToday() && $slotDateTime->lte($now)) {
                $isAvailable = false;
                $status = 'past';
                $statusLabel = 'وقت مضى';
            }

            // 2. Block check
            if ($isAvailable) {
                foreach ($partialBlocks as $block) {
                    $blockStart = Carbon::parse($block->start_time);
                    $blockEnd = Carbon::parse($block->end_time);
                    if ($slotStart->lt($blockEnd) && $slotEnd->gt($blockStart)) {
                        $isAvailable = false;
                        $status = 'blocked';
                        $statusLabel = 'غير متاح (محظور)';
                        break;
                    }
                }
            }

            // 3. Booking check
            if ($isAvailable) {
                foreach ($existingBookings as $booking) {
                    $bookingStart = Carbon::parse($booking->start_time);
                    $bookingEnd = Carbon::parse($booking->end_time);
                    if ($slotStart->lt($bookingEnd) && $slotEnd->gt($bookingStart)) {
                        $isAvailable = false;
                        $status = 'booked';
                        $statusLabel = 'محجوز';
                        break;
                    }
                }
            }

            $slotItem = [
                'start' => $slotStartStr,
                'end' => $slotEndStr,
                'time_formatted' => Carbon::createFromFormat('H:i', $slotStartStr)->translatedFormat('g:i A'),
                'is_available' => $isAvailable,
                'status' => $status,
                'status_label' => $statusLabel,
            ];

            $allSlots[] = $slotItem;

            if ($isAvailable) {
                $availableSlots[] = $slotItem;
            } else {
                $unavailableSlots[] = $slotItem;
            }

            $currentSlot->addMinutes(15);
        }

        return [
            'date' => $dateStr,
            'day_name' => $date->translatedFormat('l'),
            'is_day_available' => count($availableSlots) > 0,
            'service_id' => $service->id,
            'service_title' => $service->title,
            'duration' => $duration,
            'summary' => [
                'total_slots' => count($allSlots),
                'available_count' => count($availableSlots),
                'booked_or_unavailable_count' => count($unavailableSlots),
            ],
            'available_slots' => $availableSlots,
            'unavailable_slots' => $unavailableSlots,
            'booked_slots' => array_values(array_filter($unavailableSlots, fn($s) => $s['status'] === 'booked')),
            'all_slots' => $allSlots,
        ];
    }
}

