@extends('layouts.admin')

@section('title', 'تعديل الصفحة التعريفية')

@section('content')
<form action="{{ route('admin.portfolio.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <!-- Main Portfolio Data -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-4 text-teal" style="color: var(--accent-color);"><i class="bi bi-file-earmark-person-fill me-1"></i> البيانات الشخصية والمهنية</h5>
                
                <div class="mb-3">
                    <label class="form-label small fw-bold">اللقب والصفة المهنية</label>
                    <input type="text" name="title" class="form-control form-control-lg" value="{{ $profile->title }}" required>
                </div>
                
                <div class="mb-4">
                    <label class="form-label small fw-bold">النبذة التعريفية (السيرة الذاتية المفصلة)</label>
                    <textarea name="bio" class="form-control" rows="8" required>{{ $profile->bio }}</textarea>
                </div>
            </div>

            <!-- Dynamic Lists (Education, Experience, Certificates, Specialties) -->
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-4 text-teal" style="color: var(--accent-color);"><i class="bi bi-list-check me-1"></i> المؤهلات والخبرات والشهادات</h5>

                <!-- Education -->
                <div class="mb-4">
                    <label class="form-label small fw-bold d-flex justify-content-between align-items-center">
                        <span>التعليم والشهادات الأكاديمية</span>
                        <button type="button" class="btn btn-sm btn-outline-teal" onclick="addItem('education-list', 'education')"><i class="bi bi-plus-circle-fill"></i> إضافة بند</button>
                    </label>
                    <div id="education-list" class="d-flex flex-column gap-2">
                        @if(!empty($profile->education))
                            @foreach($profile->education as $edu)
                                <div class="input-group">
                                    <input type="text" name="education[]" class="form-control" value="{{ $edu }}">
                                    <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove()"><i class="bi bi-trash"></i></button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Experience -->
                <div class="mb-4">
                    <label class="form-label small fw-bold d-flex justify-content-between align-items-center">
                        <span>الخبرات المهنية السابقة والحالية</span>
                        <button type="button" class="btn btn-sm btn-outline-teal" onclick="addItem('experience-list', 'experience')"><i class="bi bi-plus-circle-fill"></i> إضافة بند</button>
                    </label>
                    <div id="experience-list" class="d-flex flex-column gap-2">
                        @if(!empty($profile->experience))
                            @foreach($profile->experience as $exp)
                                <div class="input-group">
                                    <input type="text" name="experience[]" class="form-control" value="{{ $exp }}">
                                    <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove()"><i class="bi bi-trash"></i></button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Certificates -->
                <div class="mb-4">
                    <label class="form-label small fw-bold d-flex justify-content-between align-items-center">
                        <span>الإنجازات والتكريمات الأخرى</span>
                        <button type="button" class="btn btn-sm btn-outline-teal" onclick="addItem('certificates-list', 'certificates')"><i class="bi bi-plus-circle-fill"></i> إضافة بند</button>
                    </label>
                    <div id="certificates-list" class="d-flex flex-column gap-2">
                        @if(!empty($profile->certificates))
                            @foreach($profile->certificates as $cert)
                                <div class="input-group">
                                    <input type="text" name="certificates[]" class="form-control" value="{{ $cert }}">
                                    <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove()"><i class="bi bi-trash"></i></button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Specialties -->
                <div class="mb-4">
                    <label class="form-label small fw-bold d-flex justify-content-between align-items-center">
                        <span>مجاالت التخصص الدقيقة</span>
                        <button type="button" class="btn btn-sm btn-outline-teal" onclick="addItem('specialties-list', 'specialties')"><i class="bi bi-plus-circle-fill"></i> إضافة بند</button>
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
            </div>
        </div>

        <!-- Social links & Clinic Gallery -->
        <div class="col-lg-4">
            <!-- Social links -->
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-4 text-teal" style="color: var(--accent-color);"><i class="bi bi-share-fill me-1"></i> روابط التواصل الاجتماعي</h5>
                <div class="mb-3">
                    <label class="form-label small fw-bold"><i class="bi bi-facebook text-primary me-1"></i> فيسبوك</label>
                    <input type="url" name="facebook" class="form-control" value="{{ $profile->social_links['facebook'] ?? '' }}" placeholder="https://facebook.com/...">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold"><i class="bi bi-twitter-x text-dark me-1"></i> إكس (تويتر)</label>
                    <input type="url" name="twitter" class="form-control" value="{{ $profile->social_links['twitter'] ?? '' }}" placeholder="https://twitter.com/...">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold"><i class="bi bi-instagram text-danger me-1"></i> إنستغرام</label>
                    <input type="url" name="instagram" class="form-control" value="{{ $profile->social_links['instagram'] ?? '' }}" placeholder="https://instagram.com/...">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold"><i class="bi bi-linkedin text-primary me-1"></i> لينكد إن</label>
                    <input type="url" name="linkedin" class="form-control" value="{{ $profile->social_links['linkedin'] ?? '' }}" placeholder="https://linkedin.com/...">
                </div>
            </div>

            <!-- Clinic celebrations gallery -->
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-4 text-teal" style="color: var(--accent-color);"><i class="bi bi-images me-1"></i> معرض صور العيادة والفعاليات</h5>
                <div class="mb-4">
                    <label class="form-label small fw-bold">رفع صور جديدة المعرض (يمكن اختيار عدة صور)</label>
                    <input type="file" name="gallery_images[]" class="form-control" multiple accept="image/*">
                </div>

                <div class="row g-2" id="gallery-container">
                    @if(!empty($profile->gallery))
                        @foreach($profile->gallery as $imgUrl)
                            <div class="col-6 position-relative gallery-item-wrapper" id="img-{{ md5($imgUrl) }}">
                                <img src="{{ $imgUrl }}" alt="Gallery" class="img-thumbnail" style="height: 100px; width: 100%; object-fit: cover;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle p-1" style="width: 25px; height: 25px; line-height: 12px;" onclick="deleteGalleryImage('{{ $imgUrl }}', '{{ md5($imgUrl) }}')">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Save button floating/sticky -->
            <div class="d-grid">
                <button type="submit" class="btn btn-premium btn-lg py-3 shadow"><i class="bi bi-save me-1"></i> حفظ جميع البيانات</button>
            </div>
        </div>
    </div>
</form>
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
                document.getElementById('img-' + idHash).remove();
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
