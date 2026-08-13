@extends('layouts.app')

@section('title', 'د. يونس أحمد - استشاري جراحة العظام والمناظير والمفاصل')

@section('content')
<!-- Hero Section -->
<section class="hero-section py-5 my-4">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-md-6 order-2 order-md-1">
                <span class="badge bg-teal mb-3 px-3 py-2 text-white" style="background-color: var(--accent-color);">مرحباً بكم في عيادتنا</span>
                <h1 class="display-4 fw-extrabold mb-3 text-dark">
                    {{ $profile->user->name ?? 'د. يونس أحمد' }}
                </h1>
                <p class="fs-4 text-secondary mb-4">
                    {{ $profile->title ?? 'استشاري أول جراحة العظام والمناظير والمفاصل الصناعية' }}
                </p>
                <div class="d-flex gap-3">
                    <a href="#booking-section" class="btn btn-premium btn-lg">احجز موعدك الآن</a>
                    <a href="#about" class="btn btn-premium-outline btn-lg">اقرأ المزيد</a>
                </div>
            </div>
            <div class="col-md-6 order-1 order-md-2 text-center">
                <img src="https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=600&q=80" alt="Doctor" class="img-fluid rounded-4 shadow-lg border border-white border-4" style="max-height: 400px; object-fit: cover;">
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-5 bg-white border-top border-bottom border-light">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-md-8">
                <h2 class="fw-bold">السيرة المهنية والخبرات الطبية</h2>
                <div class="mx-auto" style="width: 80px; height: 4px; background: var(--accent-color); border-radius: 2px;"></div>
                <p class="text-secondary mt-3 fs-5">{{ $profile->bio ?? 'نبذة سريعة عن الدكتور' }}</p>
            </div>
        </div>

        <div class="row gy-4">
            <!-- Education -->
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm p-4">
                    <h4 class="fw-bold mb-4 text-teal" style="color: var(--accent-color);">
                        <i class="bi bi-mortarboard-fill me-2"></i> الدرجات الأكاديمية والتعليم
                    </h4>
                    <div class="timeline-premium">
                        @if(!empty($profile->education))
                            @foreach($profile->education as $edu)
                                <div class="timeline-item">
                                    <h6 class="fw-bold mb-1">{{ $edu }}</h6>
                                </div>
                            @endforeach
                        @else
                            <p class="text-secondary">لا توجد بيانات حالياً.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Experience -->
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm p-4">
                    <h4 class="fw-bold mb-4 text-teal" style="color: var(--accent-color);">
                        <i class="bi bi-briefcase-fill me-2"></i> الخبرات المهنية والمناصب
                    </h4>
                    <div class="timeline-premium">
                        @if(!empty($profile->experience))
                            @foreach($profile->experience as $exp)
                                <div class="timeline-item">
                                    <h6 class="fw-bold mb-1">{{ $exp }}</h6>
                                </div>
                            @endforeach
                        @else
                            <p class="text-secondary">لا توجد بيانات حالياً.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Specialties -->
            <div class="col-col-12 mt-4">
                <div class="card border-0 shadow-sm p-4">
                    <h4 class="fw-bold mb-4 text-teal" style="color: var(--accent-color);">
                        <i class="bi bi-award-fill me-2"></i> مجالات التخصص والاهتمامات الطبية
                    </h4>
                    <div class="row g-3">
                        @if(!empty($profile->specialties))
                            @foreach($profile->specialties as $spec)
                                <div class="col-md-6 d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success fs-5 me-2"></i>
                                    <span class="fw-semibold text-secondary">{{ $spec }}</span>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services / Consultations Section -->
<section id="services" class="py-5">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-md-8">
                <h2 class="fw-bold">الخدمات والاستشارات المتاحة</h2>
                <div class="mx-auto" style="width: 80px; height: 4px; background: var(--accent-color); border-radius: 2px;"></div>
                <p class="text-secondary mt-3">نقدم أنواع مختلفة من الاستشارات الطبية بما يناسب وقتك وحالتك الصحية.</p>
            </div>
        </div>

        <div class="row gy-4">
            @foreach($services as $service)
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 card-glass p-3 text-center">
                        <div class="card-body d-flex flex-column h-100">
                            <div class="fs-1 text-teal mb-3" style="color: var(--accent-color);">
                                @if(str_contains($service->title, 'فيديو') || str_contains($service->title, 'أونلاين'))
                                    <i class="bi bi-laptop"></i>
                                @elseif(str_contains($service->title, 'مستعجل'))
                                    <i class="bi bi-lightning-fill text-warning"></i>
                                @elseif(str_contains($service->title, 'متابعة'))
                                    <i class="bi bi-arrow-repeat"></i>
                                @else
                                    <i class="bi bi-hospital"></i>
                                @endif
                            </div>
                            <h5 class="fw-bold card-title mb-2">{{ $service->title }}</h5>
                            <p class="text-secondary small card-text mb-4">{{ $service->description }}</p>
                            
                            <div class="mt-auto">
                                <hr class="text-secondary my-3">
                                <div class="d-flex justify-content-between align-items-center mb-3 px-2">
                                    <span class="text-secondary small"><i class="bi bi-clock me-1"></i> {{ $service->duration }} دقيقة</span>
                                    <span class="fw-bold text-dark fs-5">${{ number_format($service->price, 2) }}</span>
                                </div>
                                <a href="#booking-section" class="btn btn-premium w-100 btn-sm" onclick="selectService({{ $service->id }})">اختر هذه الخدمة</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section id="gallery" class="py-5 bg-white border-top border-bottom border-light">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-md-8">
                <h2 class="fw-bold">معرض الصور والفعاليات والنجاحات</h2>
                <div class="mx-auto" style="width: 80px; height: 4px; background: var(--accent-color); border-radius: 2px;"></div>
                <p class="text-secondary mt-3">لقطات من المؤتمرات العلمية الدولية واحتفالات العيادة بالنجاح والتعافي الكامل لمرضانا.</p>
            </div>
        </div>

        <div class="row g-4">
            @if(!empty($profile->gallery))
                @foreach($profile->gallery as $imgUrl)
                    <div class="col-md-6 col-lg-3 col-sm-6">
                        <div class="gallery-img-wrapper">
                            <img src="{{ $imgUrl }}" alt="Clinic Event">
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-center text-secondary col-12">لا توجد صور في المعرض حالياً.</p>
            @endif
        </div>
    </div>
</section>

<!-- Booking Section (Quick Booking Widget) -->
<section id="booking-section" class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card border-0 shadow-lg p-4 card-glass">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <i class="bi bi-calendar-check text-teal fs-1" style="color: var(--accent-color);"></i>
                            <h3 class="fw-bold mt-2">الحجز السريع للمواعيد</h3>
                            <p class="text-secondary small">اختر نوع الاستشارة، اليوم، والوقت المتاح لإتمام الحجز والدفع فورياً</p>
                        </div>

                        <!-- Step 1: Select Service -->
                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="bi bi-heart-pulse-fill text-teal me-1"></i> 1. اختر الخدمة الطبية المطلوبة:</label>
                            <select id="service-select" class="form-select form-select-lg" onchange="onServiceOrDateChange()">
                                <option value="" disabled selected>-- اختر الخدمة --</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" data-price="{{ $service->price }}">{{ $service->title }} ({{ $service->duration }} دقيقة) - ${{ $service->price }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Step 2: Select Date -->
                        <div class="mb-4 d-none" id="date-step">
                            <label class="form-label fw-bold"><i class="bi bi-calendar-date text-teal me-1"></i> 2. اختر تاريخ الزيارة:</label>
                            <input type="date" id="date-input" class="form-control form-control-lg" min="{{ today()->format('Y-m-d') }}" onchange="onServiceOrDateChange()">
                            <small class="text-secondary">أيام العيادة المتاحة: السبت، الأحد، الاثنين، الأربعاء، الخميس.</small>
                        </div>

                        <!-- Step 3: Select Slot -->
                        <div class="mb-4 d-none" id="slots-step">
                            <label class="form-label fw-bold"><i class="bi bi-clock-history text-teal me-1"></i> 3. المواعيد المتاحة (ساعات العمل):</label>
                            <div id="slots-container" class="row row-cols-3 row-cols-sm-4 g-2">
                                <!-- Slots generated here by JS -->
                            </div>
                            <div id="slots-loader" class="text-center d-none py-3">
                                <div class="spinner-border text-teal" role="status">
                                    <span class="visually-hidden">تحميل...</span>
                                </div>
                            </div>
                            <div id="slots-empty" class="alert alert-warning d-none text-center small">
                                لا توجد مواعيد متاحة في هذا اليوم، يرجى اختيار تاريخ آخر.
                            </div>
                        </div>

                        <!-- Step 4: Patient Info & Checkout -->
                        <div class="d-none" id="checkout-step">
                            <hr class="my-4 text-secondary">
                            <h5 class="fw-bold mb-3"><i class="bi bi-person-fill text-teal me-1"></i> 4. بيانات المريض وإتمام الحجز:</h5>
                            
                            @auth
                                <div class="alert alert-success d-flex align-items-center mb-3">
                                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                    <div>
                                        أنت مسجل دخول باسم: <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->email }})
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info py-2 small mb-3">
                                    <i class="bi bi-info-circle-fill me-1"></i> سيتم إنشاء ملف طبي للمريض وحساب تلقائي في موقعنا للمتابعة لاحقاً.
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">الاسم الكامل</label>
                                        <input type="text" id="patient-name" class="form-control" placeholder="الاسم ثلاثي" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">رقم الجوال</label>
                                        <input type="tel" id="patient-phone" class="form-control" placeholder="رقم الجوال لتأكيد الموعد" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">البريد الإلكتروني</label>
                                        <input type="email" id="patient-email" class="form-control" placeholder="لإرسال إيصال الدفع" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">كلمة مرور لملفك الطبي (أرقام وحروف)</label>
                                        <input type="password" id="patient-password" class="form-control" placeholder="الحد الأدنى 8 خانات" required>
                                    </div>
                                </div>
                            @endauth

                            <!-- Summary -->
                            <div class="card bg-light border-0 p-3 mb-4">
                                <h6 class="fw-bold mb-2">ملخص الحجز والرسوم:</h6>
                                <div class="d-flex justify-content-between small text-secondary mb-1">
                                    <span>الاستشارة المطلوبة:</span>
                                    <span id="summary-service">-</span>
                                </div>
                                <div class="d-flex justify-content-between small text-secondary mb-1">
                                    <span>التاريخ والوقت:</span>
                                    <span id="summary-datetime">-</span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between fw-bold text-dark fs-5">
                                    <span>المجموع المستحق:</span>
                                    <span id="summary-price">$0.00</span>
                                </div>
                            </div>

                            <!-- Stripe Form container -->
                            <div class="mb-4 d-none" id="stripe-container">
                                <label class="form-label fw-bold text-danger"><i class="bi bi-credit-card-2-front-fill me-1"></i> معلومات الدفع الإلكتروني آمن 100%:</label>
                                <div id="card-element" class="form-control p-3 bg-white" style="border-radius: 10px;">
                                    <!-- Stripe Elements card goes here -->
                                </div>
                                <div id="card-errors" class="text-danger small mt-2" role="alert"></div>
                            </div>

                            <button id="book-btn" class="btn btn-premium btn-lg w-100 py-3 mt-2" onclick="processCheckout()">
                                <i class="bi bi-credit-card-fill me-1"></i> إتمام الحجز والدفع الآمن
                            </button>
                            <div id="booking-msg" class="text-center mt-3 text-danger small"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Booking Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-5">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                <h3 class="fw-bold mt-4">تم الحجز والدفع بنجاح!</h3>
                <p class="text-secondary mt-2">رقم الحجز الخاص بك هو: <strong id="success-ref" class="text-dark"></strong></p>
                <p class="text-secondary small">تم إرسال تأكيد الموعد إلى بريدك الإلكتروني بنجاح.</p>
                <a href="{{ route('patient.dashboard') }}" class="btn btn-premium mt-4">الانتقال للوحة التحكم لمتابعة الحجوزات</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    let selectedSlot = null;
    let stripe = null;
    let elements = null;
    let card = null;

    // Initialize Stripe JS
    const stripeKey = "{{ config('services.stripe.key') }}";
    if (stripeKey && !stripeKey.includes('placeholder')) {
        stripe = Stripe(stripeKey);
        elements = stripe.elements();
        card = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#32325d',
                    fontFamily: 'Tajawal, sans-serif',
                }
            }
        });
    }

    function selectService(id) {
        document.getElementById('service-select').value = id;
        onServiceOrDateChange();
    }

    function onServiceOrDateChange() {
        const serviceSelect = document.getElementById('service-select');
        const dateInput = document.getElementById('date-input');
        const dateStep = document.getElementById('date-step');
        const slotsStep = document.getElementById('slots-step');
        const checkoutStep = document.getElementById('checkout-step');

        const serviceId = serviceSelect.value;
        if (!serviceId) return;

        dateStep.classList.remove('d-none');

        const date = dateInput.value;
        if (!date) {
            slotsStep.classList.add('d-none');
            checkoutStep.classList.add('d-none');
            return;
        }

        // Fetch slots from API
        slotsStep.classList.remove('d-none');
        document.getElementById('slots-container').innerHTML = '';
        document.getElementById('slots-loader').classList.remove('d-none');
        document.getElementById('slots-empty').classList.add('d-none');
        checkoutStep.classList.add('d-none');
        selectedSlot = null;

        fetch(`/api/slots?service_id=${serviceId}&date=${date}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('slots-loader').classList.add('d-none');
                if (data.length === 0) {
                    document.getElementById('slots-empty').classList.remove('d-none');
                    return;
                }

                data.forEach(slot => {
                    const btn = document.createElement('div');
                    btn.className = 'col';
                    btn.innerHTML = `<div class="slot-btn" onclick="selectSlot('${slot.start}', '${slot.end}', this)">${slot.start}</div>`;
                    document.getElementById('slots-container').appendChild(btn);
                });
            })
            .catch(err => {
                console.error(err);
                document.getElementById('slots-loader').classList.add('d-none');
                document.getElementById('slots-empty').classList.remove('d-none');
            });
    }

    function selectSlot(start, end, element) {
        // Deselect previous slot
        const active = document.querySelector('.slot-btn.selected');
        if (active) active.classList.remove('selected');

        // Select new slot
        element.classList.add('selected');
        selectedSlot = { start, end };

        // Show checkout
        const checkoutStep = document.getElementById('checkout-step');
        checkoutStep.classList.remove('d-none');

        // Update Summary
        const serviceSelect = document.getElementById('service-select');
        const option = serviceSelect.options[serviceSelect.selectedIndex];
        const serviceTitle = option.text.split(' (')[0];
        const servicePrice = option.dataset.price;
        const date = document.getElementById('date-input').value;

        document.getElementById('summary-service').innerText = serviceTitle;
        document.getElementById('summary-datetime').innerText = `${date} - الساعة ${start}`;
        document.getElementById('summary-price').innerText = `$${parseFloat(servicePrice).toFixed(2)}`;

        // Show Stripe Elements if Stripe key exists
        if (stripe) {
            document.getElementById('stripe-container').classList.remove('d-none');
            card.mount('#card-element');
        } else {
            document.getElementById('stripe-container').classList.add('d-none');
        }
    }

    function processCheckout() {
        const serviceSelect = document.getElementById('service-select');
        const dateInput = document.getElementById('date-input');
        const msgDiv = document.getElementById('booking-msg');
        const bookBtn = document.getElementById('book-btn');

        msgDiv.innerText = '';
        
        const payload = {
            service_id: serviceSelect.value,
            date: dateInput.value,
            start_time: selectedSlot.start,
        };

        const isAuthenticated = "{{ Auth::check() }}";
        if (!isAuthenticated) {
            payload.name = document.getElementById('patient-name').value;
            payload.email = document.getElementById('patient-email').value;
            payload.phone = document.getElementById('patient-phone').value;
            payload.password = document.getElementById('patient-password').value;

            if (!payload.name || !payload.email || !payload.phone || !payload.password) {
                msgDiv.innerText = 'يرجى إكمال جميع الحقول الشخصية أولاً.';
                return;
            }
        }

        bookBtn.disabled = true;
        bookBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> جاري معالجة الطلب...`;

        // Send check-out request
        fetch('/api/booking/checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                msgDiv.innerText = data.message || 'حدث خطأ غير متوقع. يرجى مراجعة البيانات.';
                bookBtn.disabled = false;
                bookBtn.innerHTML = `<i class="bi bi-credit-card-fill me-1"></i> إتمام الحجز والدفع الآمن`;
                return;
            }

            // Mock mode checkout
            if (!stripe || data.client_secret.startsWith('mock_')) {
                // If Stripe key is missing or mock mode, confirm immediately
                mockWebhookConfirmation(data.booking_reference);
                return;
            }

            // Real Stripe checkout
            stripe.confirmCardPayment(data.client_secret, {
                payment_method: {
                    card: card,
                    billing_details: {
                        name: isAuthenticated ? "{{ Auth::user()->name ?? '' }}" : payload.name,
                        email: isAuthenticated ? "{{ Auth::user()->email ?? '' }}" : payload.email,
                    }
                }
            }).then(function(result) {
                if (result.error) {
                    msgDiv.innerText = 'فشل الدفع: ' + result.error.message;
                    bookBtn.disabled = false;
                    bookBtn.innerHTML = `<i class="bi bi-credit-card-fill me-1"></i> إتمام الحجز والدفع الآمن`;
                } else {
                    if (result.paymentIntent.status === 'succeeded') {
                        // Success modal
                        document.getElementById('success-ref').innerText = data.booking_reference;
                        const modal = new bootstrap.Modal(document.getElementById('successModal'));
                        modal.show();
                    }
                }
            });
        })
        .catch(err => {
            console.error(err);
            msgDiv.innerText = 'حدث خطأ في الاتصال بالخادم. يرجى المحاولة لاحقاً.';
            bookBtn.disabled = false;
            bookBtn.innerHTML = `<i class="bi bi-credit-card-fill me-1"></i> إتمام الحجز والدفع الآمن`;
        });
    }

    // A helper function for local demonstration to simulate the Stripe Webhook confirming the payment in database
    function mockWebhookConfirmation(reference) {
        fetch('/stripe/webhook', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                type: 'payment_intent.succeeded',
                data: {
                    object: {
                        id: 'mock_pi_succeed',
                        // Find matching booking payment
                        booking_reference: reference
                    }
                }
            })
        }).then(() => {
            // Success modal
            document.getElementById('success-ref').innerText = reference;
            const modal = new bootstrap.Modal(document.getElementById('successModal'));
            modal.show();
        });
    }
</script>
@endsection
