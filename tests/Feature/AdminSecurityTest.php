<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\DoctorProfile;
use App\Models\Doctor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::create([
            'name' => 'د. يونس المرشدي',
            'email' => 'dr.yonis@example.com',
            'phone' => '+9647700000000',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        DoctorProfile::create([
            'user_id' => $admin->id,
            'title' => 'دكتور يونس المرشدي',
            'bio' => 'معالج نفسي',
            'education' => ['CBT'],
        ]);

        Doctor::create([
            'id' => 1,
            'user_id' => $admin->id,
            'name' => 'د. يونس المرشدي',
            'is_active' => true,
        ]);
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_patient_cannot_access_admin_dashboard(): void
    {
        $patient = User::create([
            'name' => 'مريض تجريبي',
            'email' => 'patient.test@example.com',
            'phone' => '+9647711111111',
            'role' => 'patient',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($patient)->get('/admin/dashboard');

        // Must NOT return 200 OK. Should redirect to patient dashboard with error
        $response->assertRedirect(route('patient.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_patient_cannot_access_admin_bookings(): void
    {
        $patient = User::create([
            'name' => 'مريض تجريبي 2',
            'email' => 'patient2@example.com',
            'phone' => '+9647722222222',
            'role' => 'patient',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($patient)->get('/admin/bookings');

        // Blocked by admin middleware before even reaching CheckPermission
        $response->assertRedirect(route('patient.dashboard'));
    }

    public function test_patient_json_request_to_admin_is_forbidden_403(): void
    {
        $patient = User::create([
            'name' => 'مريض تجريبي 3',
            'email' => 'patient3@example.com',
            'phone' => '+9647733333333',
            'role' => 'patient',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($patient)->getJson('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }
}

