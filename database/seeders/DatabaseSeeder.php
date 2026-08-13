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
            'name' => 'د. يونس أحمد',
            'email' => 'dr.yonis@example.com',
            'phone' => '+201234567890',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        // 2. Create Patient User (for testing)
        User::create([
            'name' => 'أحمد محمد علي',
            'email' => 'patient@example.com',
            'phone' => '+201111222333',
            'role' => 'patient',
            'password' => Hash::make('password'),
        ]);

        // 3. Create Doctor Profile
        DoctorProfile::create([
            'user_id' => $doctorUser->id,
            'title' => 'استشاري أول جراحة العظام والمفاصل والمناظير',
            'bio' => 'دكتور يونس أحمد، استشاري جراحة العظام والمفاصل والمناظير وإصابات الملاعب. يمتلك خبرة واسعة تمتد لأكثر من 15 عاماً في تشخيص وعلاج مشاكل المفاصل وإجراء العمليات الدقيقة باستخدام أحدث التقنيات الطبية العالمية. حائز على البورد الأوروبي والعديد من العضويات الأكاديمية والمهنية المرموقة.',
            'education' => [
                'زمالة كلية الجراحين الملكية البريطانية (FRCS)',
                'دكتوراه جراحة العظام والكسور - جامعة عين شمس',
                'عضو الجمعية الأوروبية لجراحة مناظير الركبة وإصابات الملاعب (ESSKA)',
                'بكالوريوس الطب والجراحة العامة بتقدير امتياز مع مرتبة الشرف'
            ],
            'experience' => [
                'رئيس قسم جراحة العظام بمستشفى الشروق الدولي (2020 - حالياً)',
                'استشاري جراحة المفاصل الصناعية والمناظير بالمستشفى السعودي الألماني (2016 - 2020)',
                'طبيب زمالة بمستشفيات كينجز كوليدج في لندن، المملكة المتحدة (2012 - 2015)',
                'عضو هيئة التدريس بقسم جراحة العظام بجامعة عين شمس'
            ],
            'certificates' => [
                'شهادة التميز الأكاديمي والعملي في جراحات الركبة المتقدمة من سويسرا',
                'عضو معتمد في الجمعية السويسرية لتثبيت الكسور (AO Trauma)',
                'درع التكريم من نقابة الأطباء للتميز المهني لعام 2024'
            ],
            'specialties' => [
                'عمليات تغيير مفاصل الركبة والحوض بمفاصل صناعية حديثة',
                'علاج تمزق الرباط الصليبي والغضاريف الهلالية بالمناظير',
                'تثبيت الكسور المعقدة والتشوهات العظمية للكبار والأطفال',
                'علاج الخشونة وحقن المفاصل باستخدام البلازما الغنية بالصفائح الدموية (PRP)'
            ],
            'social_links' => [
                'facebook' => 'https://facebook.com',
                'twitter' => 'https://twitter.com',
                'instagram' => 'https://instagram.com',
                'linkedin' => 'https://linkedin.com'
            ],
            'gallery' => [
                'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1584515979956-d9f6e5d09982?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1629909613654-28e377c37b09?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1516549655169-df83a0774514?auto=format&fit=crop&w=800&q=80'
            ]
        ]);

        // 4. Create Services
        Service::create([
            'title' => 'استشارة في العيادة (كشف أول مرة)',
            'description' => 'كشف أولي دقيق في العيادة لتشخيص آلام المفاصل أو الكسيرات وتشخيصها باستخدام الأشعة، مع وضع خطة علاجية متكاملة.',
            'price' => 50.00,
            'duration' => 30, // 30 minutes
            'is_active' => true,
        ]);

        Service::create([
            'title' => 'كشف مستعجل (دون حجز مسبق)',
            'description' => 'جلسة كشف عاجلة مخصصة للحالات الطارئة أو الكسور المفاجئة والآلام الحادة التي لا تحتمل الانتظار.',
            'price' => 90.00,
            'duration' => 20, // 20 minutes
            'is_active' => true,
        ]);

        Service::create([
            'title' => 'استشارة مرئية أونلاين (فيديو)',
            'description' => 'جلسة استشارية عن بعد لمدة 30 دقيقة عبر الإنترنت لمناقشة نتائج التحاليل والأشعة وتحديث خطة العلاج والدواء.',
            'price' => 40.00,
            'duration' => 30, // 30 minutes
            'is_active' => true,
        ]);

        Service::create([
            'title' => 'استشارة متابعة (إعادة)',
            'description' => 'متابعة دورية لمراقبة تحسن الحالة، والتأكد من نجاح خطة العلاج، أو تغيير الجبائر وإزالة الخيوط الجراحية.',
            'price' => 20.00,
            'duration' => 15, // 15 minutes
            'is_active' => true,
        ]);

        // 5. Create Availabilities (Working Hours)
        // 0 = Sunday, 1 = Monday, 2 = Tuesday, 3 = Wednesday, 4 = Thursday, 5 = Friday, 6 = Saturday
        // We will seed working days: Saturday, Sunday, Monday, Wednesday, Thursday
        // Time format: HH:MM
        $workingDays = [
            0 => ['start' => '14:00', 'end' => '20:00'], // Sunday
            1 => ['start' => '14:00', 'end' => '20:00'], // Monday
            3 => ['start' => '14:00', 'end' => '20:00'], // Wednesday
            4 => ['start' => '14:00', 'end' => '20:00'], // Thursday
            6 => ['start' => '12:00', 'end' => '18:00'], // Saturday
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
