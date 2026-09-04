@extends('layouts.admin')

@section('title', 'إدارة محتوى الموقع والوسائط وآراء العملاء')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    <!-- Main Left Column: Doctor Profile & Images -->
    <div class="col-lg-8">
        
        <!-- SECTION 1: Site Images & Media Uploads -->
        <div class="card border-0 shadow-sm p-4 mb-4 rounded-4">
            <h5 class="fw-bold mb-4 text-primary d-flex align-items-center gap-2">
                <i class="bi bi-image-fill fs-4"></i> إدارة صور الموقع والهوية البصرية
            </h5>

            <form action="{{ route('admin.portfolio.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3 mb-4">
                    <!-- 1. Web Hero Image -->
                    <div class="col-md-6">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label small fw-bold m-0 text-dark">
                                <i class="bi bi-laptop text-primary me-1"></i> صورة المعالج للموقع (Web Hero)
                            </label>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill small">الموقع الإلكتروني</span>
                        </div>
                        <div class="d-flex align-items-center gap-3 border p-3 rounded-4 bg-light">
                            <div class="position-relative">
                                @if(!empty($profile->hero_image))
                                    <img src="{{ $profile->hero_image }}" alt="Web Hero Image" class="rounded-3 shadow-sm border" style="width: 75px; height: 75px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 75px; height: 75px;"><i class="bi bi-person fs-2"></i></div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <input type="file" name="hero_image_file" class="form-control form-control-sm mb-1" accept="image/*">
                                <span class="text-muted small" style="font-size:0.75rem;">تظهر في واجهة الموقع لشاشات الكمبيوتر والمتصفح</span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Mobile App Hero Image (API) -->
                    <div class="col-md-6">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label small fw-bold m-0 text-dark">
                                <i class="bi bi-phone text-success me-1"></i> صورة المعالج للموبايل (Mobile & API Hero)
                            </label>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill small">تطبيق الموبايل والـ API</span>
                        </div>
                        <div class="d-flex align-items-center gap-3 border p-3 rounded-4 bg-light">
                            <div class="position-relative">
                                @if(!empty($profile->hero_image_mobile))
                                    <img src="{{ $profile->hero_image_mobile }}" alt="Mobile Hero Image" class="rounded-3 shadow-sm border border-success" style="width: 75px; height: 75px; object-fit: cover;">
                                @elseif(!empty($profile->hero_image))
                                    <img src="{{ $profile->hero_image }}" alt="Fallback Hero Image" class="rounded-3 shadow-sm border opacity-75" style="width: 75px; height: 75px; object-fit: cover;" title="مأخوذة تلقائياً من صورة الموقع">
                                @else
                                    <div class="bg-success text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 75px; height: 75px;"><i class="bi bi-phone fs-2"></i></div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <input type="file" name="hero_image_mobile_file" class="form-control form-control-sm mb-1" accept="image/*">
                                <span class="text-muted small" style="font-size:0.75rem;">مخصصة للـ API وتطبيق الموبايل (أبعاد رأسية ملائمة للشاشات)</span>
                            </div>
                        </div>
                    </div>

                    <!-- About Image -->
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">صورة قسم "عن المعالج" (About Section)</label>
                        <div class="d-flex align-items-center gap-3 border p-3 rounded-4 bg-light">
                            <div class="position-relative">
                                @if(!empty($profile->about_image))
                                    <img src="{{ $profile->about_image }}" alt="About Image" class="rounded-3 shadow-sm" style="width: 75px; height: 75px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 75px; height: 75px;"><i class="bi bi-person-bounding-box fs-2"></i></div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <input type="file" name="about_image_file" class="form-control form-control-sm mb-1" accept="image/*">
                                <span class="text-muted small" style="font-size:0.75rem;">تظهر في بطاقة السيرة الذاتية</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <!-- Site Logo -->
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">شعار الموقع (Site Logo)</label>
                        <div class="d-flex align-items-center gap-3 border p-3 rounded-4 bg-light">
                            <div class="position-relative">
                                @php $siteLogo = \App\Models\Setting::get('site_logo'); @endphp
                                @if(!empty($siteLogo))
                                    <img src="{{ $siteLogo }}" alt="Logo" class="rounded-3 p-1 bg-white border" style="width: 75px; height: 75px; object-fit: contain;">
                                @else
                                    <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 75px; height: 75px;"><i class="bi bi-heart-pulse fs-2"></i></div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <input type="file" name="site_logo_file" class="form-control form-control-sm mb-1" accept="image/*">
                                <span class="text-muted small" style="font-size:0.75rem;">شعار الهيدر والشرائط</span>
                            </div>
                        </div>
                    </div>

                    <!-- Clinic Gallery -->
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">معرض صور العيادة والفعاليات</label>
                        <input type="file" name="gallery_images[]" class="form-control" multiple accept="image/*">
                        <span class="text-muted small" style="font-size:0.75rem;">يمكن اختيار عدة صور دفعة واحدة</span>
                    </div>
                </div>

                <!-- Existing Gallery Grid -->
                @if(!empty($profile->gallery))
                    <div class="mb-4">
                        <label class="form-label small fw-bold mb-2">الصور الحالية في المعرض:</label>
                        <div class="row g-2" id="gallery-container">
                            @foreach($profile->gallery as $imgUrl)
                                <div class="col-3 position-relative gallery-item-wrapper" id="img-{{ md5($imgUrl) }}">
                                    <img src="{{ $imgUrl }}" alt="Gallery" class="img-thumbnail rounded-3" style="height: 90px; width: 100%; object-fit: cover;">
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;" onclick="deleteGalleryImage('{{ $imgUrl }}', '{{ md5($imgUrl) }}')">
                                        <i class="bi bi-x fs-6"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <hr class="my-4">

        <!-- SECTION 2: Bilingual Doctor Profile Data -->
                <h5 class="fw-bold mb-4 text-primary d-flex align-items-center gap-2">
                    <i class="bi bi-translate fs-4"></i> البيانات التعريفية والتخصصات (بالعربي والإنجليزي)
                </h5>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">اللقب والصفة المهنية (بالعربي)</label>
                        <input type="text" name="title" class="form-control" value="{{ $profile->title }}" placeholder="مثال: معالج نفسي ومعالج معرفي سلوكي" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Title & Qualification (English)</label>
                        <input type="text" name="title_en" class="form-control" value="{{ $profile->title_en }}" placeholder="e.g. Licensed Clinical Psychologist">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">النبذة التعريفية (بالعربي)</label>
                        <textarea name="bio" class="form-control" rows="5" required>{{ $profile->bio }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Biography (English)</label>
                        <textarea name="bio_en" class="form-control" rows="5" placeholder="Write bio in English...">{{ $profile->bio_en }}</textarea>
                    </div>
                </div>

                <!-- Dynamic Specialties (AR & EN) -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold d-flex justify-content-between align-items-center">
                            <span>مجالات التخصص (بالعربي)</span>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItem('specialties-list', 'specialties')"><i class="bi bi-plus-circle"></i> إضافة تخصص</button>
                        </label>
                        <div id="specialties-list" class="d-flex flex-column gap-2">
                            @if(!empty($profile->specialties))
                                @foreach($profile->specialties as $spec)
                                    <div class="input-group">
                                        <input type="text" name="specialties[]" class="form-control" value="{{ $spec }}">
                                        <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove()"><i class="bi bi-trash"></i></button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold d-flex justify-content-between align-items-center">
                            <span>Specialties (English)</span>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItem('specialties-en-list', 'specialties_en')"><i class="bi bi-plus-circle"></i> Add Specialty</button>
                        </label>
                        <div id="specialties-en-list" class="d-flex flex-column gap-2">
                            @if(!empty($profile->specialties_en))
                                @foreach($profile->specialties_en as $specEn)
                                    <div class="input-group">
                                        <input type="text" name="specialties_en[]" class="form-control" value="{{ $specEn }}">
                                        <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove()"><i class="bi bi-trash"></i></button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Social links -->
                <h6 class="fw-bold mb-3 text-secondary"><i class="bi bi-share me-1"></i> روابط التواصل الاجتماعي</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <input type="url" name="facebook" class="form-control" value="{{ $profile->social_links['facebook'] ?? '' }}" placeholder="فيسبوك: https://facebook.com/...">
                    </div>
                    <div class="col-md-6">
                        <input type="url" name="twitter" class="form-control" value="{{ $profile->social_links['twitter'] ?? '' }}" placeholder="إكس (تويتر): https://twitter.com/...">
                    </div>
                    <div class="col-md-6">
                        <input type="url" name="instagram" class="form-control" value="{{ $profile->social_links['instagram'] ?? '' }}" placeholder="إنستغرام: https://instagram.com/...">
                    </div>
                    <div class="col-md-6">
                        <input type="url" name="linkedin" class="form-control" value="{{ $profile->social_links['linkedin'] ?? '' }}" placeholder="لينكد إن: https://linkedin.com/...">
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill py-3 fw-bold shadow-sm">
                        <i class="bi bi-check-circle-fill me-2"></i> حفظ جميع الصور والبيانات التعريفية
                    </button>
                </div>
            </form>
        </div>

    </div>

    <!-- Right Column: Video Reels & Testimonials Management -->
    <div class="col-lg-4">

        <!-- Video Reels Management -->
        <div class="card border-0 shadow-sm p-4 mb-4 rounded-4">
            <h5 class="fw-bold mb-4 text-primary d-flex align-items-center gap-2">
                <i class="bi bi-camera-reels-fill fs-4 text-danger"></i> إدارة مقاطع الفيديو التوعوية (Reels)
            </h5>

            <!-- Add New Reel Form -->
            <form action="{{ route('admin.portfolio.reel.store') }}" method="POST" enctype="multipart/form-data" class="mb-4 p-3 border rounded-3 bg-light">
                @csrf
                <h6 class="fw-bold mb-3 text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle-fill text-primary"></i> إضافة مقطع فيديو جديد
                </h6>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-bold">عنوان المقطع (بالعربي) *</label>
                        <input type="text" name="title" class="form-control" placeholder="مثال: كيف تتغلب على نوبة الهلع في 3 دقائق؟" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Video Title (English)</label>
                        <input type="text" name="title_en" class="form-control" placeholder="e.g. 3 Quick Tips for Panic Attacks">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">المنصة (Platform)</label>
                        <select name="platform" class="form-select">
                            <option value="youtube">YouTube Shorts / يوتيوب</option>
                            <option value="tiktok">TikTok / تيك توك</option>
                            <option value="instagram">Instagram Reel / انستغرام</option>
                            <option value="direct">Direct Video / فيديو مباشر</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">ترتيب العرض (Sort Order)</label>
                        <input type="number" name="sort_order" class="form-control" value="0" min="0">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">رابط الفيديو (Video URL) *</label>
                        <input type="text" name="video_url" class="form-control" placeholder="https://youtube.com/shorts/... أو https://tiktok.com/@...">
                        <div class="form-text text-muted small" style="font-size:0.75rem;">يدعم روابط YouTube Shorts، TikTok، و Instagram Reels.</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">أو رفع ملف فيديو مباشر (MP4/WebM)</label>
                        <input type="file" name="video_file" class="form-control" accept="video/mp4,video/webm,video/mov">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">صورة الغلاف (Thumbnail Image)</label>
                        <input type="file" name="thumbnail" class="form-control" accept="image/*">
                        <div class="form-text text-muted small" style="font-size:0.75rem;">في حال إضافة رابط YouTube، سيتم جلب صورة الغلاف تلقائياً إذا تركت هذا الحقل فارغاً.</div>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold">
                            <i class="bi bi-plus-lg me-1"></i> حفظ ونشر المقطع
                        </button>
                    </div>
                </div>
            </form>

            <!-- Existing Reels List -->
            <div class="d-flex flex-column gap-2">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small fw-bold text-muted">المقاطع الحالية ({{ $reels->count() }})</span>
                </div>
                @forelse($reels as $reel)
                    <div class="p-3 border rounded-3 bg-white shadow-sm d-flex align-items-center justify-content-between gap-2">
                        <div class="d-flex align-items-center gap-3 overflow-hidden">
                            <div class="position-relative flex-shrink-0" style="width: 52px; height: 65px; border-radius: 8px; overflow: hidden; background: #111;">
                                @if($reel->thumbnail_url)
                                    <img src="{{ $reel->thumbnail_url }}" alt="Reel" class="w-100 h-100" style="object-fit: cover;">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-dark text-white">
                                        <i class="bi bi-play-circle fs-4"></i>
                                    </div>
                                @endif
                                <span class="position-absolute bottom-0 end-0 start-0 text-center text-white py-0" style="font-size:0.6rem; background: rgba(0,0,0,0.65);">
                                    {{ ucfirst($reel->platform ?? 'video') }}
                                </span>
                            </div>
                            <div class="overflow-hidden">
                                <div class="fw-bold small text-dark text-truncate" title="{{ $reel->title }}">{{ $reel->title }}</div>
                                @if($reel->title_en)
                                    <div class="text-muted small text-truncate" style="font-size:0.75rem;">{{ $reel->title_en }}</div>
                                @endif
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="badge {{ $reel->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} border" style="font-size:0.65rem;">
                                        {{ $reel->is_active ? 'نشط' : 'معطل' }}
                                    </span>
                                    @if($reel->video_url && $reel->video_url !== '#')
                                        <a href="{{ $reel->video_url }}" target="_blank" class="small text-primary text-decoration-none" style="font-size:0.7rem;">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> فتح الرابط
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-1 flex-shrink-0">
                            <!-- Edit Button -->
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1" onclick='openEditReelModal(@json($reel))' title="تعديل المقطع">
                                <i class="bi bi-pencil-square"></i> تعديل
                            </button>

                            <!-- Delete Form -->
                            <form action="{{ route('admin.portfolio.reel.delete', $reel->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف مقطع الفيديو هذا؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1" title="حذف المقطع">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4 small bg-light rounded-3">لا توجد مقاطع فيديو مضافة بعد.</div>
                @endforelse
            </div>
        </div>

        <!-- SECTION 4: Testimonials / Client Reviews Management -->
        <div class="card border-0 shadow-sm p-4 mb-4 rounded-4">
            <h5 class="fw-bold mb-3 text-primary d-flex align-items-center gap-2">
                <i class="bi bi-chat-quote-fill fs-4 text-warning"></i> آراء العملاء والمراجعين
            </h5>

            <!-- Add Testimonial Form -->
            <form action="{{ route('admin.portfolio.testimonial.store') }}" method="POST" enctype="multipart/form-data" class="mb-4 p-3 bg-light rounded-4 border">
                @csrf
                <div class="mb-2">
                    <label class="form-label small fw-bold">اسم المراجع (بالعربي) *</label>
                    <input type="text" name="client_name_ar" class="form-control form-control-sm" placeholder="مثال: أمل الشمري" required>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Client Name (English)</label>
                    <input type="text" name="client_name_en" class="form-control form-control-sm" placeholder="e.g. Amal A.">
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">نص الرأي (بالعربي) *</label>
                    <textarea name="content_ar" class="form-control form-control-sm" rows="2" placeholder="اكتب تجربة المراجع هنا..." required></textarea>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Review Content (English)</label>
                    <textarea name="content_en" class="form-control form-control-sm" rows="2" placeholder="Write review in English..."></textarea>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">التقييم (1-5)</label>
                        <select name="rating" class="form-select form-select-sm">
                            <option value="5" selected>⭐⭐⭐⭐⭐ (5/5)</option>
                            <option value="4">⭐⭐⭐⭐ (4/5)</option>
                            <option value="3">⭐⭐⭐ (3/5)</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">صورة المراجع (اختياري)</label>
                        <input type="file" name="avatar_file" class="form-control form-control-sm" accept="image/*">
                    </div>
                </div>
                <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill fw-bold"><i class="bi bi-plus-lg me-1"></i> إضافة رأي المراجع</button>
            </form>

            <!-- Testimonials List -->
            <div class="d-flex flex-column gap-2">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small fw-bold text-muted">الآراء الحالية ({{ $testimonials->count() }})</span>
                </div>
                @forelse($testimonials as $t)
                    <div class="p-3 border rounded-3 bg-white position-relative shadow-sm">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div class="d-flex align-items-center gap-2">
                                @if($t->client_avatar)
                                    <img src="{{ $t->client_avatar }}" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;" alt="Avatar">
                                @endif
                                <div>
                                    <div class="fw-bold text-dark small">{{ $t->client_name_ar }} @if($t->client_name_en)<span class="text-muted font-monospace" style="font-size:0.75rem;">({{ $t->client_name_en }})</span>@endif</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <button type="button" class="btn btn-sm btn-outline-primary border-0 p-1" onclick='openEditTestimonialModal(@json($t))' title="تعديل"><i class="bi bi-pencil-square"></i></button>
                                <form action="{{ route('admin.portfolio.testimonial.delete', $t->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا التقييم؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1" title="حذف"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                        <div class="text-warning small mb-1">
                            @for($i = 0; $i < $t->rating; $i++)
                                <i class="bi bi-star-fill text-warning"></i>
                            @endfor
                        </div>
                        <p class="text-secondary small mb-0 lh-sm">{{ $t->content_ar }}</p>
                    </div>
                @empty
                    <div class="text-center text-muted py-3 small bg-light rounded-3">لا توجد آراء عملاء مضافة بعد.</div>
                @endforelse
            </div>
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     EDIT REEL MODAL
═══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editReelModal" tabindex="-1" aria-labelledby="editReelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form id="editReelForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-primary" id="editReelModalLabel">
                        <i class="bi bi-pencil-square me-1"></i> تعديل مقطع الفيديو (Reel)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold">عنوان المقطع (بالعربي) *</label>
                            <input type="text" name="title" id="edit_reel_title" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Video Title (English)</label>
                            <input type="text" name="title_en" id="edit_reel_title_en" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">المنصة (Platform)</label>
                            <select name="platform" id="edit_reel_platform" class="form-select">
                                <option value="youtube">YouTube Shorts / يوتيوب</option>
                                <option value="tiktok">TikTok / تيك توك</option>
                                <option value="instagram">Instagram Reel / انستغرام</option>
                                <option value="direct">Direct Video / فيديو مباشر</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">ترتيب العرض (Sort Order)</label>
                            <input type="number" name="sort_order" id="edit_reel_sort_order" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">رابط الفيديو (Video URL)</label>
                            <input type="text" name="video_url" id="edit_reel_video_url" class="form-control" placeholder="https://youtube.com/shorts/...">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">تغيير ملف الفيديو (اختياري)</label>
                            <input type="file" name="video_file" class="form-control" accept="video/mp4,video/webm,video/mov">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">تغيير صورة الغلاف (Thumbnail)</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <img id="edit_reel_thumbnail_preview" src="" alt="Preview" class="rounded-2 border" style="width: 50px; height: 60px; object-fit: cover; display: none;">
                                <input type="file" name="thumbnail" class="form-control" accept="image/*">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_reel_is_active">
                                <label class="form-check-label small fw-bold" for="edit_reel_is_active">عرض المقطع في الصفحة الرئيسية (نشط)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="bi bi-check-lg me-1"></i> حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     EDIT TESTIMONIAL MODAL
═══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editTestimonialModal" tabindex="-1" aria-labelledby="editTestimonialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form id="editTestimonialForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-primary" id="editTestimonialModalLabel">
                        <i class="bi bi-pencil-square me-1"></i> تعديل رأي العميل
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-2">
                        <label class="form-label small fw-bold">اسم المراجع (بالعربي) *</label>
                        <input type="text" name="client_name_ar" id="edit_t_name_ar" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Client Name (English)</label>
                        <input type="text" name="client_name_en" id="edit_t_name_en" class="form-control form-control-sm">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">نص الرأي (بالعربي) *</label>
                        <textarea name="content_ar" id="edit_t_content_ar" class="form-control form-control-sm" rows="3" required></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Review Content (English)</label>
                        <textarea name="content_en" id="edit_t_content_en" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">التقييم (1-5)</label>
                            <select name="rating" id="edit_t_rating" class="form-select form-select-sm">
                                <option value="5">⭐⭐⭐⭐⭐ (5/5)</option>
                                <option value="4">⭐⭐⭐⭐ (4/5)</option>
                                <option value="3">⭐⭐⭐ (3/5)</option>
                                <option value="2">⭐⭐ (2/5)</option>
                                <option value="1">⭐ (1/5)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">تغيير الصورة (اختياري)</label>
                            <input type="file" name="avatar_file" class="form-control form-control-sm" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="bi bi-check-lg me-1"></i> حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function addItem(containerId, fieldName) {
        const container = document.getElementById(containerId);
        const group = document.createElement('div');
        group.className = 'input-group';
        group.innerHTML = `
            <input type="text" name="${fieldName}[]" class="form-control" placeholder="بند جديد">
            <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove()"><i class="bi bi-trash"></i></button>
        `;
        container.appendChild(group);
    }

    function deleteGalleryImage(imageUrl, idHash) {
        if (!confirm('هل أنت متأكد من حذف هذه الصورة نهائياً من المعرض؟')) return;

        fetch('{{ route('admin.portfolio.image.delete') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({ image_url: imageUrl })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const el = document.getElementById('img-' + idHash);
                if (el) el.remove();
            } else {
                alert('فشل حذف الصورة.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('حدث خطأ في الاتصال بالخادم.');
        });
    }

    function openEditReelModal(reel) {
        const form = document.getElementById('editReelForm');
        form.action = "{{ url('admin/portfolio/reel') }}/" + reel.id;
        
        document.getElementById('edit_reel_title').value = reel.title || '';
        document.getElementById('edit_reel_title_en').value = reel.title_en || '';
        document.getElementById('edit_reel_platform').value = reel.platform || 'youtube';
        document.getElementById('edit_reel_sort_order').value = reel.sort_order ?? 0;
        document.getElementById('edit_reel_video_url').value = reel.video_url || '';
        document.getElementById('edit_reel_is_active').checked = reel.is_active !== false && reel.is_active != 0;

        const preview = document.getElementById('edit_reel_thumbnail_preview');
        if (reel.thumbnail_url) {
            preview.src = reel.thumbnail_url;
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }

        const modal = new bootstrap.Modal(document.getElementById('editReelModal'));
        modal.show();
    }

    function openEditTestimonialModal(t) {
        const form = document.getElementById('editTestimonialForm');
        form.action = "{{ url('admin/portfolio/testimonial') }}/" + t.id;

        document.getElementById('edit_t_name_ar').value = t.client_name_ar || '';
        document.getElementById('edit_t_name_en').value = t.client_name_en || '';
        document.getElementById('edit_t_content_ar').value = t.content_ar || '';
        document.getElementById('edit_t_content_en').value = t.content_en || '';
        document.getElementById('edit_t_rating').value = t.rating || 5;

        const modal = new bootstrap.Modal(document.getElementById('editTestimonialModal'));
        modal.show();
    }
</script>
@endsection
