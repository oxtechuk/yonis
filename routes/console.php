<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('clinic:seed-content', function () {
    // 1. Update Service ID 3 (and default prices for any missing services)
    $s3 = \App\Models\Service::find(3);
    if ($s3) {
        $s3->update([
            'price' => 40.00,
            'clinic_price' => 45.00,
            'video_price' => 40.00,
            'voice_price' => 35.00,
            'chat_price' => 25.00,
            'type' => 'both',
        ]);
        $this->info("✅ Service ID 3 updated successfully with prices: Clinic: $45 | Video: $40 | Voice: $35 | Chat: $25");
    } else {
        \App\Models\Service::create([
            'id' => 3,
            'title' => 'استشارة مرئية أونلاين (فيديو)',
            'description' => 'جلسة استشارية عن بعد لمدة 30 دقيقة عبر الإنترنت لمناقشة نتائج التحاليل والأشعة وتحديث خطة العلاج والدواء.',
            'type' => 'both',
            'price' => 40.00,
            'clinic_price' => 45.00,
            'chat_price' => 25.00,
            'voice_price' => 35.00,
            'video_price' => 40.00,
            'duration' => 30,
            'is_active' => true,
        ]);
        $this->info("✅ Service ID 3 created successfully with custom channel prices.");
    }

    // Ensure all services have channel prices
    foreach (\App\Models\Service::all() as $srv) {
        $base = (float) $srv->price;
        $updates = [];
        if (is_null($srv->clinic_price)) $updates['clinic_price'] = $base;
        if (is_null($srv->video_price)) $updates['video_price'] = $base;
        if (is_null($srv->voice_price)) $updates['voice_price'] = round($base * 0.9, 2);
        if (is_null($srv->chat_price)) $updates['chat_price'] = round($base * 0.75, 2);
        if (!empty($updates)) {
            $srv->update($updates);
        }
    }
    $this->info("✅ All services validated with channel prices.");

    // 2. Add New Reels
    $reels = [
        [
            'title' => 'كيف تتخلص من التفكير الزائد (Overthinking) والقلق المستمر؟',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80',
            'video_url' => 'https://www.tiktok.com/@younisalmurshed/video/7123456789012345678',
            'platform' => 'tiktok',
            'duration' => 45,
            'sort_order' => 1,
            'is_active' => true,
        ],
        [
            'title' => 'خطوات عملية للتعامل مع نوبات الهلع والخوف المفاجئ - د. يونس المرشدي',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=600&q=80',
            'video_url' => 'https://www.youtube.com/shorts/dQw4w9WgXcQ',
            'platform' => 'youtube',
            'duration' => 60,
            'sort_order' => 2,
            'is_active' => true,
        ],
        [
            'title' => 'قواعد الحوار الصحي بين الزوجين وتجنب الخلافات المتكررة',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=600&q=80',
            'video_url' => 'https://www.tiktok.com/@younisalmurshed/video/7987654321098765432',
            'platform' => 'tiktok',
            'duration' => 30,
            'sort_order' => 3,
            'is_active' => true,
        ],
        [
            'title' => '5 علامات واضحة تدل على الاحتراق النفسي والمهني (Burnout)',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=600&q=80',
            'video_url' => 'https://www.youtube.com/shorts/5burnoutsigns',
            'platform' => 'youtube',
            'duration' => 50,
            'sort_order' => 4,
            'is_active' => true,
        ],
        [
            'title' => 'تقنية 5-4-3-2-1 السحرية لتهدئة العقل الفوري أثناء التوتر',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?auto=format&fit=crop&w=600&q=80',
            'video_url' => 'https://www.tiktok.com/@younisalmurshed/video/groundingtechnique',
            'platform' => 'tiktok',
            'duration' => 40,
            'sort_order' => 5,
            'is_active' => true,
        ],
        [
            'title' => 'كيف تحمي نفسك من الاستنزاف العاطفي في العلاقات السامة؟',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1499209974431-9dddcece7f88?auto=format&fit=crop&w=600&q=80',
            'video_url' => 'https://www.youtube.com/shorts/healthyboundaries',
            'platform' => 'youtube',
            'duration' => 55,
            'sort_order' => 6,
            'is_active' => true,
        ],
    ];

    foreach ($reels as $r) {
        \App\Models\Reel::updateOrCreate(['title' => $r['title']], $r);
    }
    $this->info("✅ " . count($reels) . " Reels seeded successfully.");

    // 3. Add Client Reviews / Testimonials
    $testimonials = [
        [
            'client_name_ar' => 'محمد السعدي',
            'client_name_en' => 'Mohammed Al-Saadi',
            'client_avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80',
            'rating' => 5,
            'content_ar' => 'تجربة متميزة جداً مع الدكتور يونس المرشدي. ساعدتني جلسات العلاج المعرفي السلوكي في التخلص من نوبات الهلع التي عانيت منها لسنوات، وأصبحت أمارس حياتي اليومية بثقة كاملة.',
            'content_en' => 'Exceptional experience with Dr. Younis Al-Murshedi. The CBT sessions helped me overcome panic attacks that I had suffered from for years.',
            'is_active' => true,
        ],
        [
            'client_name_ar' => 'زينب الكرخي',
            'client_name_en' => 'Zainab Al-Karkhi',
            'client_avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=200&q=80',
            'rating' => 5,
            'content_ar' => 'أسلوب راقٍ واستماع عميق بدون أي أحكام مسبقة. الاستشارات الزوجية أعادت الدفء والاستقرار لبيتنا بعد فترة خلافات صعبة، ممتنة جداً لجهود الدكتور وإخلاصه.',
            'content_en' => 'Professional approach and deep listening without judgment. The marriage counseling restored warmth and stability to our family.',
            'is_active' => true,
        ],
        [
            'client_name_ar' => 'عمر البغدادي',
            'client_name_en' => 'Omar Al-Baghdadi',
            'client_avatar' => 'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?auto=format&fit=crop&w=200&q=80',
            'rating' => 5,
            'content_ar' => 'السهولة في الحجز والمتابعة عبر مكالمات الفيديو كانت رائعة وفي منتهى الخصوصية. أنصح كل من يمر بضغط نفسي بالتواصل مع د. يونس.',
            'content_en' => 'Easy booking flow and seamless video sessions. I highly recommend anyone experiencing severe stress to consult Dr. Younis.',
            'is_active' => true,
        ],
        [
            'client_name_ar' => 'د. هدى الجبوري',
            'client_name_en' => 'Dr. Huda Al-Jubouri',
            'client_avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=200&q=80',
            'rating' => 5,
            'content_ar' => 'كدكتورة، أقدّر جداً النهج العلمي المنظم الذي يتبعه د. يونس. ساعدني في تجاوز مرحلة إرهاق واحتراق مهني حادة، والآن أتمتع بتوازن ممتاز بين عملي وحياتي.',
            'content_en' => 'As a physician, I highly appreciate Dr. Younis scientific approach. He helped me overcome severe burnout.',
            'is_active' => true,
        ],
        [
            'client_name_ar' => 'أحمد العبيدي',
            'client_name_en' => 'Ahmed Al-Obeidi',
            'client_avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=200&q=80',
            'rating' => 5,
            'content_ar' => 'كنت متردداً ومتحفظاً بخصوص استشارة معالج نفسي، لكن منذ الجلسة الأولى شعرت بأمان كبير وراحة نفسية. أسلوب عملي ومبسط ساعدني في التغلب على القلق الاجتماعي.',
            'content_en' => 'I was hesitant about psychological counseling, but from session one I felt immense psychological safety and comfort.',
            'is_active' => true,
        ],
        [
            'client_name_ar' => 'مريم النجفي',
            'client_name_en' => 'Maryam Al-Najafi',
            'client_avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80',
            'rating' => 5,
            'content_ar' => 'المواعيد الأونلاين دقيقة جداً والتعامل في قمة الاحترام. التمارين السلوكية التي وجهني لها الدكتور أحدثت فارقاً حقيقياً في تخفيف التوتر اليومي.',
            'content_en' => 'Online sessions were prompt and deeply respectful. The behavioral exercises made a true difference in relieving daily tension.',
            'is_active' => true,
        ],
    ];

    foreach ($testimonials as $t) {
        \App\Models\Testimonial::updateOrCreate(['client_name_ar' => $t['client_name_ar']], $t);
    }
    $this->info("✅ " . count($testimonials) . " Testimonials seeded successfully.");
})->purpose('Seed additional reels, client testimonials, and set prices for service 3');
