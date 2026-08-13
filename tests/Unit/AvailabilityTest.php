<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use App\Models\Availability;
use App\Models\Booking;
use App\Services\AvailabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AvailabilityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test slot generation is correct.
     */
    public function test_slots_are_generated_correctly()
    {
        // 1. Create a service
        $myService = Service::create([
            'title' => 'كشف عظام',
            'description' => 'استشارة',
            'price' => 50,
            'duration' => 30, // 30 minutes
            'is_active' => true,
        ]);

        // 2. Create availability (Sunday, 14:00 to 16:00)
        Availability::create([
            'day_of_week' => 0, // Sunday
            'start_time' => '14:00:00',
            'end_time' => '16:00:00',
        ]);

        $serviceInstance = new AvailabilityService();

        // Target Date (A future Sunday)
        $sundayDate = Carbon::now()->next(Carbon::SUNDAY)->format('Y-m-d');

        // Fetch slots
        $slots = $serviceInstance->getAvailableSlots($myService->id, $sundayDate);

        // Expected slots: should generate starting from 14:00
        $this->assertNotEmpty($slots);
        $this->assertEquals('14:00', $slots[0]['start']);
    }
}
