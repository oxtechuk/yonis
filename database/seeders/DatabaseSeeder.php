<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\DoctorProfile;
use App\Models\Service;
use App\Models\Availability;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Reel;
use App\Models\Testimonial;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with authentic Dr. Younis Al-Murshedi data.
     */
    public function run(): void
    {
        // 1. Create Doctor Admin Account (د. يونس المرشدي)
        $doctorUser = User::updateOrCreate(
            ['email' => 'dr.yonis@example.com'],
            [
                'name' => 'د. يونس المرشدي',
                'phone' => '+9647700000000',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Create Realistic Registered Patients (for testing registered vs new user flows)
        $patient1 = User::updateOrCreate(
            ['phone' => '+9647701234567'],
            [
                'name' => 'أحمد محمد عبد الله',
                'email' => 'ahmed@example.com',
                'role' => 'patient',
                'password' => Hash::make('password'),
            ]
        );

        $patient2 = User::updateOrCreate(
            ['phone' => '+9647809876543'],
            [
                'name' => 'سارة علي محمود',
                'email' => 'sara@example.com',
                'role' => 'patient',
                'password' => Hash::make('password'),
            ]
        );

        // 3. Create Doctor Profile (دكتور يونس المرشدي - استشاري العلاج النفسي والسلوكي)
        DoctorProfile::updateOrCreate(
            ['user_id' => $doctorUser->id],
            [
                'title' => 'دكتور يونس المرشدي - استشاري العلاج النفسي والسلوكي والطب النفسي',
                'title_en' => 'Dr. Younis Al-Murshedi - Consultant Clinical Psychologist & Psychotherapist',
                'bio' => 'دكتور يونس المرشدي، استشاري العلاج النفسي والسلوكي مع خبرة متميزة في معالجة اضطرابات القلق، الاكتئاب، نوبات الهلع، والصدمات النفسية. أقدم استشارات متخصصة فردية وزوجية وأسرية، وأعتمد على أحدث بروتوكولات العلاج المعرفي السلوكي (CBT) والعلاج الجدلي السلوكي (DBT) لدعم العملاء في استعادة توازنهم النفسي وجودة حياتهم.',
                'bio_en' => 'Dr. Younis Al-Murshedi is a licensed Consultant Psychotherapist specializing in Cognitive Behavioral Therapy (CBT) and Dialectical Behavior Therapy (DBT) for anxiety, depression, stress management, and marriage counseling.',
                'hero_image' => 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=800&q=80',
                'about_image' => 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&w=800&q=80',
                'education' => [
                    'دكتوراه في علم النفس الكلينيكي والعلاج النفسي',
                    'ماجستير الصحة النفسية والعلاج المعرفي السلوكي (CBT)',
                    'دبلوم عالي في الاستشارات الزوجية والعلاقات الأسرية',
                    'بكالوريوس علم النفس والعلوم السلوكية - كلية الآداب'
                ],
                'experience' => [
                    'استشاري العلاج النفسي والسلوكي - عيادة د. يونس المرشدي (2014 - حالياً)',
                    'أخصائي أول في تشخيص وعلاج اضطرابات القلق، نوبات الهلع، والاكتئاب',
                    'مؤسس برامج الاستشارات النفسية الرقمية والتعافي السلوكي عن بُعد',
                    'محاضر ومدرّب معتمد في إدارة الضغوط وتطوير الذات والتوازن النفسي'
                ],
                'certificates' => [
                    'ترخيص ممارسة العلاج النفسي من نقابة الأطباء والأخصائيين النفسيين',
                    'شهادة الاعتماد الدولية في العلاج المعرفي السلوكي (CBT Accredited)',
                    'شهادة التميز المهني في تقديم الاستشارات الرقمية والصحة النفسية 2025',
                    'عضوية الجمعية العالمية للصحة النفسية والعلاج السلوكي'
                ],
                'specialties' => [
                    'اضطراب القلق ونوبات الهلع',
                    'الاكتئاب وتقلبات المزاج',
                    'الاستشارات الزوجية والأسرية',
                    'الصدمات النفسية والتعافي منها',
                    'الوسواس القهري (OCD)',
                    'فرط الحركة وتشتت الانتباه (ADHD)',
                    'إدارة الضغوط وتطوير الثقة بالنفس'
                ],
                'specialties_en' => [
                    'Anxiety & Panic Disorders',
                    'Depression & Mood Management',
                    'Couples & Family Counseling',
                    'Trauma & PTSD Recovery',
                    'Obsessive-Compulsive Disorder (OCD)',
                    'ADHD Support & Coaching',
                    'Stress Management & Self-Development'
                ],
                'social_links' => [
                    'gumroad' => 'https://younisalmurshed.gumroad.com/l/srjlvw?wanted=true',
                    'whatsapp' => '+9647700000000',
                    'tiktok' => 'https://tiktok.com/@younisalmurshed',
                    'instagram' => 'https://instagram.com/younisalmurshed',
                    'youtube' => 'https://youtube.com/@younisalmurshed',
                    'facebook' => 'https://facebook.com/younisalmurshed',
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1584515979956-d9f6e5d09982?auto=format&fit=crop&w=800&q=80'
                ]
            ]
        );

        // 4. Create Services with Real Gumroad External Payment Links
        $gumroadLink = 'https://younisalmurshed.gumroad.com/l/srjlvw?wanted=true';
        Service::query()->delete();

        $s1 = Service::create([
            'title' => 'جلسة استشارة نفسية وتقييم أولي - 30 دقيقة',
            'description' => 'جلسة استشارية أولية لتشخيص الحالة النفسية، تقييم أعراض التوتر والقلق، ووضع الخطة العلاجية المناسبة.',
            'type' => 'both',
            'price' => 50.00,
            'clinic_price' => 50.00,
            'chat_price' => 40.00,
            'voice_price' => 45.00,
            'video_price' => 50.00,
            'payment_url' => $gumroadLink,
            'duration' => 30,
            'is_active' => true,
        ]);

        $s2 = Service::create([
            'title' => 'جلسة علاج معرفي سلوكي مكثفة (CBT) - 45 دقيقة',
            'description' => 'جلسة علاجية متعمقة تركز على تعديل الأنماط الفكرية السلبية وعلاج نوبات الهلع والاكتئاب والوسواس القهري.',
            'type' => 'both',
            'price' => 75.00,
            'clinic_price' => 75.00,
            'chat_price' => 60.00,
            'voice_price' => 70.00,
            'video_price' => 75.00,
            'payment_url' => $gumroadLink,
            'duration' => 45,
            'is_active' => true,
        ]);

        $s3 = Service::create([
            'title' => 'استشارة العلاقات الأسرية والزوجية - 60 دقيقة',
            'description' => 'جلسة إرشادية مخصصة للزوجين لحل الخلافات، تحسين مهارات التواصل، وإعادة بناء التفاهم والاستقرار العاطفي.',
            'type' => 'both',
            'price' => 100.00,
            'clinic_price' => 100.00,
            'chat_price' => 80.00,
            'voice_price' => 90.00,
            'video_price' => 100.00,
            'payment_url' => $gumroadLink,
            'duration' => 60,
            'is_active' => true,
        ]);

        $s4 = Service::create([
            'title' => 'جلسة دعم نفسي وإرشاد سريع - 15 دقيقة',
            'description' => 'استشارة سريعة وفورية للحصول على توجيه عاجل في أوقات التوتر وضغوط العمل ونوبات القلق المفاجئة.',
            'type' => 'both',
            'price' => 30.00,
            'clinic_price' => 30.00,
            'chat_price' => 25.00,
            'voice_price' => 28.00,
            'video_price' => 30.00,
            'payment_url' => $gumroadLink,
            'duration' => 15,
            'is_active' => true,
        ]);

        // 5. Create Testimonials
        Testimonial::updateOrCreate(
            ['client_name_ar' => 'محمد السعدي'],
            [
                'client_name_en' => 'Mohammed Al-Saadi',
                'client_avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80',
                'rating' => 5,
                'content_ar' => 'تجربة متميزة جداً مع الدكتور يونس المرشدي. ساعدتني جلسات العلاج المعرفي السلوكي في التخلص من نوبات الهلع التي عانيت منها لسنوات.',
                'content_en' => 'Exceptional experience with Dr. Younis Al-Murshedi. The CBT sessions helped me overcome panic attacks that I had suffered from for years.',
                'is_active' => true,
            ]
        );

        Testimonial::updateOrCreate(
            ['client_name_ar' => 'زينب الكرخي'],
            [
                'client_name_en' => 'Zainab Al-Karkhi',
                'client_avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=200&q=80',
                'rating' => 5,
                'content_ar' => 'أسلوب راقٍ واستماع عميق بدون أي أحكام مسبقة. الاستشارات الزوجية أعادت الدفء والاستقرار لبيتنا، ممتنة جداً لجهود الدكتور.',
                'content_en' => 'Professional approach and deep listening without judgment. The marriage counseling restored warmth and stability to our family.',
                'is_active' => true,
            ]
        );

        Testimonial::updateOrCreate(
            ['client_name_ar' => 'عمر البغدادي'],
            [
                'client_name_en' => 'Omar Al-Baghdadi',
                'client_avatar' => 'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?auto=format&fit=crop&w=200&q=80',
                'rating' => 5,
                'content_ar' => 'السهولة في الحجز عبر الرابط والمتابعة عبر مكالمات الفيديو كانت رائعة. أنصح كل من يمر بضغط نفسي بالتواصل مع د. يونس.',
                'content_en' => 'Easy booking flow and seamless video sessions. I highly recommend anyone experiencing severe stress to consult Dr. Younis.',
                'is_active' => true,
            ]
        );

        Testimonial::updateOrCreate(
            ['client_name_ar' => 'د. هدى الجبوري'],
            [
                'client_name_en' => 'Dr. Huda Al-Jubouri',
                'client_avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=200&q=80',
                'rating' => 5,
                'content_ar' => 'كدكتورة، أقدّر جداً النهج العلمي المنظم الذي يتبعه د. يونس. ساعدني في تجاوز مرحلة إرهاق واحتراق مهني حادة، والآن أتمتع بتوازن ممتاز بين عملي وحياتي.',
                'content_en' => 'As a physician, I highly appreciate Dr. Younis scientific approach. He helped me overcome severe burnout.',
                'is_active' => true,
            ]
        );

        Testimonial::updateOrCreate(
            ['client_name_ar' => 'أحمد العبيدي'],
            [
                'client_name_en' => 'Ahmed Al-Obeidi',
                'client_avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=200&q=80',
                'rating' => 5,
                'content_ar' => 'كنت متردداً ومتحفظاً بخصوص استشارة معالج نفسي، لكن منذ الجلسة الأولى شعرت بأمان كبير وراحة نفسية. أسلوب عملي ومبسط ساعدني في التغلب على القلق الاجتماعي.',
                'content_en' => 'I was hesitant about psychological counseling, but from session one I felt immense psychological safety and comfort.',
                'is_active' => true,
            ]
        );

        Testimonial::updateOrCreate(
            ['client_name_ar' => 'مريم النجفي'],
            [
                'client_name_en' => 'Maryam Al-Najafi',
                'client_avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80',
                'rating' => 5,
                'content_ar' => 'المواعيد الأونلاين دقيقة جداً والتعامل في قمة الاحترام. التمارين السلوكية التي وجهني لها الدكتور أحدثت فارقاً حقيقياً في تخفيف التوتر اليومي.',
                'content_en' => 'Online sessions were prompt and deeply respectful. The behavioral exercises made a true difference in relieving daily tension.',
                'is_active' => true,
            ]
        );

        // 6. Create Reels / Video Testimonials
        Reel::updateOrCreate(
            ['title' => 'كيف تتخلص من التفكير الزائد (Overthinking) والقلق المستمر؟'],
            [
                'thumbnail_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80',
                'video_url' => 'https://www.tiktok.com/@younisalmurshed/video/7123456789012345678',
                'platform' => 'tiktok',
                'duration' => 45,
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        Reel::updateOrCreate(
            ['title' => 'خطوات عملية للتعامل مع نوبات الهلع والخوف المفاجئ - د. يونس المرشدي'],
            [
                'thumbnail_url' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=600&q=80',
                'video_url' => 'https://www.youtube.com/shorts/dQw4w9WgXcQ',
                'platform' => 'youtube',
                'duration' => 60,
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        Reel::updateOrCreate(
            ['title' => 'قواعد الحوار الصحي بين الزوجين وتجنب الخلافات المتكررة'],
            [
                'thumbnail_url' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=600&q=80',
                'video_url' => 'https://www.tiktok.com/@younisalmurshed/video/7987654321098765432',
                'platform' => 'tiktok',
                'duration' => 30,
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        Reel::updateOrCreate(
            ['title' => '5 علامات واضحة تدل على الاحتراق النفسي والمهني (Burnout)'],
            [
                'thumbnail_url' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=600&q=80',
                'video_url' => 'https://www.youtube.com/shorts/5burnoutsigns',
                'platform' => 'youtube',
                'duration' => 50,
                'sort_order' => 4,
                'is_active' => true,
            ]
        );

        Reel::updateOrCreate(
            ['title' => 'تقنية 5-4-3-2-1 السحرية لتهدئة العقل الفوري أثناء التوتر'],
            [
                'thumbnail_url' => 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?auto=format&fit=crop&w=600&q=80',
                'video_url' => 'https://www.tiktok.com/@younisalmurshed/video/groundingtechnique',
                'platform' => 'tiktok',
                'duration' => 40,
                'sort_order' => 5,
                'is_active' => true,
            ]
        );

        Reel::updateOrCreate(
            ['title' => 'كيف تحمي نفسك من الاستنزاف العاطفي في العلاقات السامة؟'],
            [
                'thumbnail_url' => 'https://images.unsplash.com/photo-1499209974431-9dddcece7f88?auto=format&fit=crop&w=600&q=80',
                'video_url' => 'https://www.youtube.com/shorts/healthyboundaries',
                'platform' => 'youtube',
                'duration' => 55,
                'sort_order' => 6,
                'is_active' => true,
            ]
        );

        // 7. Create Working Hours / Availabilities
        $workingDays = [
            0 => ['start' => '09:00', 'end' => '21:00'], // Sunday
            1 => ['start' => '09:00', 'end' => '21:00'], // Monday
            2 => ['start' => '09:00', 'end' => '21:00'], // Tuesday
            3 => ['start' => '09:00', 'end' => '21:00'], // Wednesday
            4 => ['start' => '09:00', 'end' => '21:00'], // Thursday
            6 => ['start' => '10:00', 'end' => '18:00'], // Saturday
        ];

        Availability::truncate();
        foreach ($workingDays as $day => $times) {
            Availability::create([
                'day_of_week' => $day,
                'start_time' => $times['start'],
                'end_time' => $times['end'],
            ]);
        }

        // 8. Create Sample Bookings for full customer journey simulation
        $today = Carbon::today();
        
        // Past Completed Booking for registered patient 1
        $pastBooking = Booking::updateOrCreate(
            ['booking_reference' => 'BK-TEST001'],
            [
                'patient_id' => $patient1->id,
                'service_id' => $s2->id,
                'booking_type' => 'online',
                'consultation_type' => 'video',
                'price' => 75.00,
                'date' => $today->copy()->subDays(5)->format('Y-m-d'),
                'start_time' => '16:00:00',
                'end_time' => '16:45:00',
                'title' => 'جلسة علاج معرفي سلوكي - متابعة القلق',
                'notes' => 'تم استكمال الجلسة بنجاح وحققت نتائج ممتازة.',
                'status' => 'Completed',
            ]
        );

        Payment::updateOrCreate(
            ['booking_id' => $pastBooking->id],
            [
                'payment_intent_id' => 'gumroad_pay_test_001',
                'amount' => 75.00,
                'currency' => 'usd',
                'status' => 'Paid',
            ]
        );

        // Upcoming Confirmed Booking for registered patient 1
        $upcomingBooking = Booking::updateOrCreate(
            ['booking_reference' => 'BK-TEST002'],
            [
                'patient_id' => $patient1->id,
                'service_id' => $s1->id,
                'booking_type' => 'online',
                'consultation_type' => 'video',
                'price' => 50.00,
                'date' => $today->copy()->addDays(2)->format('Y-m-d'),
                'start_time' => '18:00:00',
                'end_time' => '18:30:00',
                'title' => 'جلسة استشارة ومتابعة دورية',
                'notes' => 'موعد عبر رابط الفيديو المباشر.',
                'status' => 'Confirmed',
            ]
        );

        Payment::updateOrCreate(
            ['booking_id' => $upcomingBooking->id],
            [
                'payment_intent_id' => 'gumroad_pay_test_002',
                'amount' => 50.00,
                'currency' => 'usd',
                'status' => 'Paid',
            ]
        );

        // 9. Core Settings
        Setting::set('api_enabled', '1');
        Setting::set('stripe_enabled', '0'); // Disabled by default, uses Gumroad links
        Setting::set('clinic_booking_enabled', '1');
        Setting::set('online_booking_enabled', '1');
        Setting::set('chat_enabled', '1');
        Setting::set('voice_enabled', '1');
        Setting::set('video_enabled', '1');
        Setting::set('default_payment_url', $gumroadLink);
    }
}
