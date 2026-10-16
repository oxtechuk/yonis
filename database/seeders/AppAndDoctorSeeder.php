<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Doctor;
use App\Models\AppSlider;
use App\Models\Setting;
use App\Models\DoctorProfile;
use App\Models\User;
use App\Models\Booking;
use App\Models\Availability;

class AppAndDoctorSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Dr. Younis exists in doctors table
        $user = User::where('role', 'admin')->first();
        $profile = DoctorProfile::first();

        $doctor = Doctor::updateOrCreate(
            ['id' => 1],
            [
                'user_id' => $user?->id,
                'name' => $user?->name ?? 'د. يونس المرشدي',
                'name_en' => 'Dr. Younis Al-Murshedi',
                'title' => $profile?->title ?? 'استشاري العلاج النفسي والسلوكي والطب النفسي',
                'title_en' => $profile?->title_en ?? 'Consultant Clinical Psychologist & Psychotherapist',
                'specialty' => 'العلاج النفسي والسلوكي',
                'specialty_en' => 'Psychotherapy & Behavioral Therapy',
                'avatar' => $profile?->hero_image ?? 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=800&q=80',
                'bio' => $profile?->bio ?? 'استشاري العلاج النفسي والسلوكي مع خبرة متميزة في معالجة اضطرابات القلق، الاكتئاب، نوبات الهلع، والصدمات النفسية.',
                'bio_en' => $profile?->bio_en ?? 'Licensed Consultant Psychotherapist specializing in Cognitive Behavioral Therapy (CBT).',
                'phone' => Setting::get('clinic_phone', '+9647800000000'),
                'email' => $user?->email ?? 'dr.yonis@example.com',
                'experience_years' => (int) ($profile?->experience_years ?? 12),
                'consultation_fee' => 50.00,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        // Associate existing bookings and availabilities with this doctor if null
        Booking::whereNull('doctor_id')->update(['doctor_id' => $doctor->id]);
        Availability::whereNull('doctor_id')->update(['doctor_id' => $doctor->id]);

        // 2. Default Splash Screen Settings
        if (!Setting::get('app_splash_title')) {
            Setting::set('app_splash_title', 'مرحباً بك في تطبيق عيادة د. يونس المرشدي');
        }
        if (!Setting::get('app_splash_subtitle')) {
            Setting::set('app_splash_subtitle', 'احجز استشارتك الطبية والنفسية بكل سهولة وسرية تامة');
        }
        if (!Setting::get('app_splash_image')) {
            Setting::set('app_splash_image', 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=1000&q=80');
        }
        if (!Setting::get('app_splash_enabled')) {
            Setting::set('app_splash_enabled', '1');
        }
        if (!Setting::get('app_splash_duration')) {
            Setting::set('app_splash_duration', '3');
        }

        // 3. Default App Sliders
        if (AppSlider::count() === 0) {
            AppSlider::create([
                'title' => 'استشارات تخصصية وسرية تامة',
                'subtitle' => 'احجز موعدك الآن مع نخبة الأطباء والاستشاريين',
                'image_path' => 'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&w=1200&q=80',
                'action_type' => 'doctor',
                'action_value' => '1',
                'sort_order' => 1,
                'is_active' => true,
            ]);

            AppSlider::create([
                'title' => 'جلسات حضورية وأونلاين',
                'subtitle' => 'استشارة فيديو، صوت، أو زيارة مباشرة لمقر العيادة',
                'image_path' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=1200&q=80',
                'action_type' => 'service',
                'action_value' => '1',
                'sort_order' => 2,
                'is_active' => true,
            ]);
        }
    }
}
