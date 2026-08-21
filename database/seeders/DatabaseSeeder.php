<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\DoctorProfile;
use App\Models\Service;
use App\Models\Availability;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Doctor Admin User
        $doctorUser = User::create([
            'name' => 'المعالج النفسي يونس المرشد',
            'email' => 'dr.yonis@example.com',
            'phone' => '+9647700000000',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        // 2. Create Patient User (for testing)
        User::create([
            'name' => 'أحمد محمد علي',
            'email' => 'patient@example.com',
            'phone' => '+966512345678',
            'role' => 'patient',
            'password' => Hash::make('password'),
        ]);

        // 3. Create Doctor Profile (Psychological Therapist Yonis Al-Murshid - Iraq)
        DoctorProfile::create([
            'user_id' => $doctorUser->id,
            'title' => 'معالج نفسي مرخص وأخصائي الاستشارات الفردية والأسرية - العراق',
            'bio' => 'معالج نفسي مرخص بخبرة تزيد عن 10 سنوات في تقديم الاستشارات النفسية الفردية والزوجية. أعتمد على العلاج المعرفي السلوكي (CBT) وأساليب الوعي التام لمساعدة الأفراد على فهم ذواتهم بشكل أعمق وتطوير آليات صحية للتعامل مع ضغوط الحياة. أؤمن بتوفير بيئة آمنة وخالية من الأحكام لدعم رحلتك نحو التعافي.',
            'education' => [
                'ماجستير علم النفس الكلينيكي والعلاج النفسي',
                'دبلوم عالي في العلاج المعرفي السلوكي (CBT)',
                'شهادة ممارسة العلاج النفسي المعتمدة من الاتحاد العربي للمعالجين النفسيين',
                'بكالوريوس علم النفس والعلوم التربوية'
            ],
            'experience' => [
                'استشاري العلاج النفسي - عيادة يونس المرشد التخصصية (2015 - حالياً)',
                'خبير العلاج المعرفي السلوكي والاستشارات الأسرية',
                'محاضر ومدرّب في مهارات الوعي التام وإدارة التوتر والقلق',
                'مقدم برامج التوعية النفسية والصحة النفسية عبر المنصات الرقمية'
            ],
            'certificates' => [
                'شهادة التميز في العلاج النفسي والخدمات الاستشارية 2024',
                'عضوية الجمعية العالمية للصحة النفسية والعلاج السلوكي',
                'شهادة الإشراف الكلينيكي على برامج التعافي من القلق والصدمات'
            ],
            'specialties' => [
                'اضطراب القلق والتوتر',
                'الاكتئاب وضغوط الحياة',
                'الاستشارات الزوجية والأسرية',
                'الوعي التام والتطوير الذاتي',
                'فرط الحركة ونقص الانتباه (ADHD)',
                'التعافي من الصدمات النفسية',
                'الإدمان والسلوكيات القهرية'
            ],
            'social_links' => [
                'facebook' => 'https://facebook.com',
                'tiktok' => 'https://tiktok.com',
                'instagram' => 'https://instagram.com',
                'youtube' => 'https://youtube.com'
            ],
            'gallery' => [
                'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=800&q=80'
            ]
        ]);

        // 4. Create Services matching app design (15m: 150 SAR, 30m: 250 SAR, 45m: 350 SAR)
        Service::create([
            'title' => 'جلسة استشارة نفسية - 15 دقيقة',
            'description' => 'جلسة فورية سريعة لتقييم الحالة النفسية وتقديم النصائح العاجلة.',
            'price' => 150.00,
            'duration' => 15,
            'is_active' => true,
        ]);

        Service::create([
            'title' => 'جلسة استشارة نفسية - 30 دقيقة',
            'description' => 'جلسة استشارية شاملة لتشخيص التوتر، القلق، وتقديم الدعم النفسي.',
            'price' => 250.00,
            'duration' => 30,
            'is_active' => true,
        ]);

        Service::create([
            'title' => 'جلسة استشارة نفسية - 45 دقيقة',
            'description' => 'جلسة علاجية مكثفة وتفصيلية لمناقشة المشاكل النفسية العميقة وتحديد خطة العلاج.',
            'price' => 350.00,
            'duration' => 45,
            'is_active' => true,
        ]);

        // 5. Create Reels / Video Testimonials (TikTok / YouTube / Direct)
        \App\Models\Reel::create([
            'title' => 'قصة نجاح مع العلاج السلوكي المعرفي - تجربة عميل',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80',
            'video_url' => 'https://www.tiktok.com/@dr.yonis/video/7123456789012345678',
            'platform' => 'tiktok',
            'duration' => 45,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        \App\Models\Reel::create([
            'title' => 'كيف تتغلب على القلق وتوتر العمل؟ د. يونس',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=600&q=80',
            'video_url' => 'https://www.youtube.com/shorts/dQw4w9WgXcQ',
            'platform' => 'youtube',
            'duration' => 60,
            'sort_order' => 2,
            'is_active' => true,
        ]);

        \App\Models\Reel::create([
            'title' => 'رأي أحد المراجعين بعد 4 جلسات استشارية',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=600&q=80',
            'video_url' => 'https://www.tiktok.com/@dr.yonis/video/7987654321098765432',
            'platform' => 'tiktok',
            'duration' => 30,
            'sort_order' => 3,
            'is_active' => true,
        ]);

        // 6. Create Availabilities (Working Hours)
        // 0 = Sunday, 1 = Monday, 2 = Tuesday, 3 = Wednesday, 4 = Thursday, 5 = Friday, 6 = Saturday
        $workingDays = [
            0 => ['start' => '09:00', 'end' => '21:00'], // Sunday
            1 => ['start' => '09:00', 'end' => '21:00'], // Monday
            2 => ['start' => '09:00', 'end' => '21:00'], // Tuesday
            3 => ['start' => '09:00', 'end' => '21:00'], // Wednesday
            4 => ['start' => '09:00', 'end' => '21:00'], // Thursday
            6 => ['start' => '10:00', 'end' => '18:00'], // Saturday
        ];

        foreach ($workingDays as $day => $times) {
            Availability::create([
                'day_of_week' => $day,
                'start_time' => $times['start'],
                'end_time' => $times['end'],
            ]);
        }
    }
}
