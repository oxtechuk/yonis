<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\DoctorProfile;
use App\Models\Doctor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds for Admin only.
     */
    public function run(): void
    {
        // 1. Create or update the Admin User
        $admin = User::updateOrCreate(
            ['email' => 'dr.yonis@example.com'],
            [
                'name' => 'د. يونس المرشدي',
                'phone' => '+9647700000000',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Ensure Doctor Profile exists
        if (!DoctorProfile::where('user_id', $admin->id)->exists()) {
            DoctorProfile::create([
                'user_id' => $admin->id,
                'title' => 'دكتور يونس المرشدي - استشاري العلاج النفسي والسلوكي والطب النفسي',
                'title_en' => 'Dr. Younis Al-Murshedi - Consultant Clinical Psychologist & Psychotherapist',
                'bio' => 'استشاري العلاج النفسي والسلوكي مع خبرة متميزة في معالجة اضطرابات القلق، الاكتئاب، نوبات الهلع، والصدمات النفسية.',
                'bio_en' => 'Licensed Consultant Psychotherapist specializing in Cognitive Behavioral Therapy (CBT).',
                'education' => [
                    'بكالوريوس علم النفس الإكلينيكي',
                    'شهادة العلاج المعرفي السلوكي CBT',
                    'معتمد في العلاج الأسري والزوجي',
                ],
            ]);
        }

        // 3. Ensure Doctor entry exists
        Doctor::updateOrCreate(
            ['id' => 1],
            [
                'user_id' => $admin->id,
                'name' => $admin->name,
                'name_en' => 'Dr. Younis Al-Murshedi',
                'title' => 'استشاري العلاج النفسي والسلوكي والطب النفسي',
                'title_en' => 'Consultant Clinical Psychologist & Psychotherapist',
                'specialty' => 'العلاج النفسي والسلوكي',
                'specialty_en' => 'Psychotherapy & Behavioral Therapy',
                'phone' => $admin->phone,
                'email' => $admin->email,
                'experience_years' => 12,
                'consultation_fee' => 50.00,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $this->command->info('');
        $this->command->info('=============================================');
        $this->command->info('  ✅ تم إنشاء وتحديث حساب الأدمن بنجاح! ');
        $this->command->info('=============================================');
        $this->command->info('  البريد الإلكتروني: dr.yonis@example.com');
        $this->command->info('  كلمة المرور:       password');
        $this->command->info('  الاسم:             د. يونس المرشدي');
        $this->command->info('  الدور (Role):      admin');
        $this->command->info('=============================================');
        $this->command->info('');
    }
}
