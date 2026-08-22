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
        
        <!-- 📸 SECTION 1: Site Images & Media Uploads -->
        <div class="card border-0 shadow-sm p-4 mb-4 rounded-4">
            <h5 class="fw-bold mb-4 text-primary d-flex align-items-center gap-2">
                <i class="bi bi-image-fill fs-4"></i> إدارة صور الموقع والهوية البصرية
            </h5>

            <form action="{{ route('admin.portfolio.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3 mb-4">
                    <!-- Hero Image -->
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">صورة المعالج الرئيسية (Hero Section)</label>
                        <div class="d-flex align-items-center gap-3 border p-3 rounded-4 bg-light">
                            <div class="position-relative">
                                @if(!empty($profile->hero_image))
                                    <img src="{{ $profile->hero_image }}" alt="Hero Image" class="rounded-3 shadow-sm" style="width: 75px; height: 75px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 75px; height: 75px;"><i class="bi bi-person fs-2"></i></div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <input type="file" name="hero_image_file" class="form-control form-control-sm mb-1" accept="image/*">
                                <span class="text-muted small" style="font-size:0.75rem;">تأثير صورة الهيرو الكبيرة</span>
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

                <!-- 👤 SECTION 2: Bilingual Doctor Profile Data -->
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

        <!-- 🎥 SECTION 3: Video Reels Management -->
        <div class="card border-0 shadow-sm p-4 mb-4 rounded-4">
            <h5 class="fw-bold mb-3 text-primary d-flex align-items-center gap-2">
                <i class="bi bi-film fs-4"></i> الفيديوهات التوعوية (Reels)
            </h5>

            <!-- Add Reel Form -->
            <form action="{{ route('admin.portfolio.reel.store') }}" method="POST" enctype="multipart/form-data" class="mb-4 p-3 bg-light rounded-4 border">
                @csrf
                <div class="mb-2">
                    <label class="form-label small fw-bold">عنوان الفيديو (بالعربي)</label>
                    <input type="text" name="title" class="form-control form-control-sm" placeholder="مثال: كيف تتغلب على القلق النفسي؟" required>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Video Title (English)</label>
                    <input type="text" name="title_en" class="form-control form-control-sm" placeholder="e.g. Overcoming Anxiety">
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">رابط الفيديو (YouTube / MP4)</label>
                    <input type="text" name="video_url" class="form-control form-control-sm" placeholder="https://www.youtube.com/watch?v=...">
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">أو رفع ملف فيديو مباشر</label>
                    <input type="file" name="video_file" class="form-control form-control-sm" accept="video/*">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">صورة الغلاف (Thumbnail)</label>
                    <input type="file" name="thumbnail_file" class="form-control form-control-sm" accept="image/*">
                </div>
                <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill fw-bold"><i class="bi bi-plus-lg me-1"></i> إضافة الفيديو</button>
            </form>

            <!-- Reels List -->
            <div class="d-flex flex-column gap-2">
                @forelse($reels as $reel)
                    <div class="d-flex align-items-center justify-content-between p-2 border rounded-3 bg-white">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $reel->thumbnail_url }}" alt="Reel" class="rounded-2" style="width: 45px; height: 45px; object-fit: cover;">
                            <div>
                                <div class="fw-bold small text-dark line-clamp-1">{{ $reel->title }}</div>
                                <div class="text-muted small" style="font-size:0.7rem;">{{ $reel->title_en ?? 'بدون عنوان إنجليزي' }}</div>
                            </div>
                        </div>
                        <form action="{{ route('admin.portfolio.reel.delete', $reel->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الفيديو؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger border-0"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                @empty
                    <div class="text-center text-muted py-3 small">لا توجد فيديوهات مضافة بعد.</div>
                @endforelse
            </div>
        </div>

        <!-- 💬 SECTION 4: Testimonials / Client Reviews Management -->
        <div class="card border-0 shadow-sm p-4 mb-4 rounded-4">
            <h5 class="fw-bold mb-3 text-primary d-flex align-items-center gap-2">
                <i class="bi bi-chat-quote-fill fs-4"></i> آراء العملاء والمراجعين
            </h5>

            <!-- Add Testimonial Form -->
            <form action="{{ route('admin.portfolio.testimonial.store') }}" method="POST" enctype="multipart/form-data" class="mb-4 p-3 bg-light rounded-4 border">
                @csrf
                <div class="mb-2">
                    <label class="form-label small fw-bold">اسم المراجع (بالعربي)</label>
                    <input type="text" name="client_name_ar" class="form-control form-control-sm" placeholder="مثال: أمل الشمري" required>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Client Name (English)</label>
                    <input type="text" name="client_name_en" class="form-control form-control-sm" placeholder="e.g. Amal A.">
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">نص الرأي (بالعربي)</label>
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
                @forelse($testimonials as $t)
                    <div class="p-3 border rounded-3 bg-white position-relative">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div class="fw-bold text-dark small">{{ $t->client_name_ar }} <span class="text-muted font-monospace">({{ $t->client_name_en ?? 'EN' }})</span></div>
                            <form action="{{ route('admin.portfolio.testimonial.delete', $t->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا التقييم؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-0 me-1"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                        <div class="text-warning small mb-1">
                            @for($i = 0; $i < $t->rating; $i++) ★ @endfor
                        </div>
                        <p class="text-secondary small mb-0 lh-sm">{{ $t->content_ar }}</p>
                    </div>
                @empty
                    <div class="text-center text-muted py-3 small">لا توجد آراء عملاء مضافة بعد.</div>
                @endforelse
            </div>
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
</script>
@endsection
