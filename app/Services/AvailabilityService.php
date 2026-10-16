<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Availability;
use App\Models\BlockedTime;
use App\Models\Booking;
use App\Models\Doctor;
use Carbon\Carbon;

class AvailabilityService
{
    /**
     * Resolve Doctor ID intelligently:
     * If explicit doctorId provided, use it.
     * If not provided, and there is only 1 active doctor, auto-select that doctor!
     * Otherwise return the first active doctor or null.
     */
    public function resolveDoctorId(?int $doctorId = null): ?int
    {
        if ($doctorId) {
            return $doctorId;
        }

        $activeDoctors = Doctor::active()->get();
        if ($activeDoctors->count() === 1) {
            return $activeDoctors->first()->id;
        }

        return $activeDoctors->first()?->id;
    }

    /**
     * Check if system currently operates in single-doctor mode
     */
    public function isSingleDoctor(): bool
    {
        return Doctor::active()->count() <= 1;
    }

    /**
     * Generate available slots for a specific service on a given date.
     *
     * @param int $serviceId
     * @param string $dateStr (YYYY-MM-DD)
     * @param int|null $doctorId
     * @return array List of available time slots (e.g., [["start" => "14:00", "end" => "14:30"], ...])
     */
    public function getAvailableSlots(int $serviceId, string $dateStr, ?int $doctorId = null): array
    {
        $resolvedDoctorId = $this->resolveDoctorId($doctorId);

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
            ->where(function ($q) use ($resolvedDoctorId) {
                if ($resolvedDoctorId) {
                    $q->whereNull('doctor_id')->orWhere('doctor_id', $resolvedDoctorId);
                }
            })
            ->whereNull('start_time')
            ->first();

        if ($blockedDay) {
            return [];
        }

        // 3. Get doctor's availability for this day of the week (or fallback to general)
        $dayOfWeek = $date->dayOfWeek;
        $generalAvailability = Availability::where('day_of_week', $dayOfWeek)
            ->where(function ($q) use ($resolvedDoctorId) {
                if ($resolvedDoctorId) {
                    $q->whereNull('doctor_id')->orWhere('doctor_id', $resolvedDoctorId);
                }
            })
            ->orderByRaw("CASE WHEN doctor_id IS NOT NULL THEN 0 ELSE 1 END")
            ->first();
        
        $workStart = $generalAvailability ? Carbon::parse($generalAvailability->start_time) : Carbon::parse('09:00');
        $workEnd = $generalAvailability ? Carbon::parse($generalAvailability->end_time) : Carbon::parse('23:00');

        // 4. Get partial blocked times for this date
        $partialBlocks = BlockedTime::where('date', $dateStr)
            ->where(function ($q) use ($resolvedDoctorId) {
                if ($resolvedDoctorId) {
                    $q->whereNull('doctor_id')->orWhere('doctor_id', $resolvedDoctorId);
                }
            })
            ->whereNotNull('start_time')
            ->whereNotNull('end_time')
            ->get();

        // 5. Get existing active bookings for this date and doctor
        $existingBookings = Booking::where('date', $dateStr)
            ->where(function ($q) use ($resolvedDoctorId) {
                if ($resolvedDoctorId) {
                    $q->where(function ($sub) use ($resolvedDoctorId) {
                        $sub->whereNull('doctor_id')->orWhere('doctor_id', $resolvedDoctorId);
                    });
                }
            })
            ->where(function ($q) {
                $q->whereIn('status', ['Confirmed', 'Completed', 'PendingPaymentReview'])
                  ->orWhere(function ($subQ) {
                      $subQ->where('status', 'AwaitingPayment')
                           ->where('created_at', '>=', Carbon::now()->subMinutes(15));
                  });
            })
            ->get();

        $duration = max((int)$service->duration, 15);
        $slots = [];
        $currentSlot = $workStart->copy();

        // Generate slots
        while ($currentSlot->copy()->addMinutes($duration)->lte($workEnd)) {
            $slotStart = $currentSlot->copy();
            $slotEnd = $currentSlot->copy()->addMinutes($duration);

            $slotStartStr = $slotStart->format('H:i');
            $slotEndStr = $slotEnd->format('H:i');

            // A. If date is TODAY, must be in future
            if ($date->isToday()) {
                $now = Carbon::now();
                $slotDateTime = Carbon::parse($dateStr . ' ' . $slotStartStr);
                if ($slotDateTime->lte($now)) {
                    $currentSlot->addMinutes(15);
                    continue;
                }
            }

            // B. Blocked overlap check
            $isBlocked = false;
            foreach ($partialBlocks as $block) {
                $blockStart = Carbon::parse($block->start_time);
                $blockEnd = Carbon::parse($block->end_time);
                if ($slotStart->lt($blockEnd) && $slotEnd->gt($blockStart)) {
                    $isBlocked = true;
                    break;
                }
            }

            if ($isBlocked) {
                $currentSlot->addMinutes(15);
                continue;
            }

            // C. Booking overlap check
            $isBooked = false;
            foreach ($existingBookings as $booking) {
                $bookingStart = Carbon::parse($booking->start_time);
                $bookingEnd = Carbon::parse($booking->end_time);
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
     * @param int|null $doctorId
     * @return array
     */
    public function getSlotsDetailed(int $serviceId, string $dateStr, ?int $doctorId = null): array
    {
        $resolvedDoctorId = $this->resolveDoctorId($doctorId);
        $doctor = $resolvedDoctorId ? Doctor::find($resolvedDoctorId) : null;
        $isSingleDoctor = $this->isSingleDoctor();

        $service = Service::find($serviceId);
        if (!$service || !$service->is_active) {
            return [
                'date' => $dateStr,
                'is_day_available' => false,
                'doctor_id' => $resolvedDoctorId,
                'is_single_doctor' => $isSingleDoctor,
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
                'doctor_id' => $resolvedDoctorId,
                'is_single_doctor' => $isSingleDoctor,
                'available_slots' => [],
                'unavailable_slots' => [],
                'all_slots' => [],
            ];
        }

        // Full day blocked check
        $blockedDay = BlockedTime::where('date', $dateStr)
            ->where(function ($q) use ($resolvedDoctorId) {
                if ($resolvedDoctorId) {
                    $q->whereNull('doctor_id')->orWhere('doctor_id', $resolvedDoctorId);
                }
            })
            ->whereNull('start_time')
            ->first();

        if ($blockedDay) {
            return [
                'date' => $dateStr,
                'is_day_available' => false,
                'message' => 'اليوم محظور بالكامل من الطبيب',
                'doctor_id' => $resolvedDoctorId,
                'doctor_name' => $doctor?->name,
                'is_single_doctor' => $isSingleDoctor,
                'available_slots' => [],
                'unavailable_slots' => [],
                'all_slots' => [],
            ];
        }

        $dayOfWeek = $date->dayOfWeek;
        $generalAvailability = Availability::where('day_of_week', $dayOfWeek)
            ->where(function ($q) use ($resolvedDoctorId) {
                if ($resolvedDoctorId) {
                    $q->whereNull('doctor_id')->orWhere('doctor_id', $resolvedDoctorId);
                }
            })
            ->orderByRaw("CASE WHEN doctor_id IS NOT NULL THEN 0 ELSE 1 END")
            ->first();

        $workStart = $generalAvailability ? Carbon::parse($generalAvailability->start_time) : Carbon::parse('09:00');
        $workEnd = $generalAvailability ? Carbon::parse($generalAvailability->end_time) : Carbon::parse('23:00');

        $partialBlocks = BlockedTime::where('date', $dateStr)
            ->where(function ($q) use ($resolvedDoctorId) {
                if ($resolvedDoctorId) {
                    $q->whereNull('doctor_id')->orWhere('doctor_id', $resolvedDoctorId);
                }
            })
            ->whereNotNull('start_time')
            ->whereNotNull('end_time')
            ->get();

        $existingBookings = Booking::where('date', $dateStr)
            ->where(function ($q) use ($resolvedDoctorId) {
                if ($resolvedDoctorId) {
                    $q->where(function ($sub) use ($resolvedDoctorId) {
                        $sub->whereNull('doctor_id')->orWhere('doctor_id', $resolvedDoctorId);
                    });
                }
            })
            ->where(function ($q) {
                $q->whereIn('status', ['Confirmed', 'Completed', 'PendingPaymentReview'])
                  ->orWhere(function ($subQ) {
                      $subQ->where('status', 'AwaitingPayment')
                           ->where('created_at', '>=', Carbon::now()->subMinutes(15));
                  });
            })
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
            'doctor_id' => $resolvedDoctorId,
            'doctor_name' => $doctor?->name,
            'is_single_doctor' => $isSingleDoctor,
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
