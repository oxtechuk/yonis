@extends('layouts.admin')

@section('title', 'إدارة الموظفين وتحديد الصلاحيات')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1"><i class="bi bi-people-fill text-primary me-2"></i> إدارة فريق العمل والموظفين</h4>
        <p class="text-muted small mb-0">إضافة موظفين جديد، تعيين أدوارهم، وتحديد الصلاحيات الدقيقة التي يُسمح لهم بالوصول إليها</p>
    </div>
    <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addStaffModal">
        <i class="bi bi-person-plus-fill me-1"></i> إضافة موظف جديد
    </button>
</div>

<!-- Staff Table Card -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted small border-bottom">
                <tr>
                    <th class="py-3 px-4">الموظف</th>
                    <th class="py-3">البريد الإلكتروني</th>
                    <th class="py-3">رقم الهاتف</th>
                    <th class="py-3">الصلاحيات الممنوحة</th>
                    <th class="py-3 text-end px-4">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($staffMembers as $staff)
                    <tr>
                        <td class="px-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 42px; height: 42px; font-size: 1.1rem;">
                                    {{ mb_substr($staff->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $staff->name }}</div>
                                    <div class="text-muted small" style="font-size: 0.75rem;">تاريخ الإضافة: {{ $staff->created_at->format('Y-m-d') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="dir-ltr text-end font-monospace text-secondary">{{ $staff->email }}</td>
                        <td class="text-secondary">{{ $staff->phone ?: 'غير محدد' }}</td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @if(!empty($staff->permissions))
                                    @foreach($staff->permissions as $permKey)
                                        @if(isset($availablePermissions[$permKey]))
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 small">
                                                <i class="bi bi-shield-check me-1"></i> {{ $availablePermissions[$permKey] }}
                                            </span>
                                        @endif
                                    @endforeach
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1 small">بدون صلاحيات</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-end px-4">
                            <button type="button" class="btn btn-sm btn-outline-primary me-1 rounded-pill" data-bs-toggle="modal" data-bs-target="#editStaffModal-{{ $staff->id }}">
                                <i class="bi bi-pencil-square me-1"></i> تعديل الصلاحيات
                            </button>
                            <form action="{{ route('admin.staff.destroy', $staff->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف حساب الموظف؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Staff Modal -->
                    <div class="modal fade" id="editStaffModal-{{ $staff->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content border-0 shadow rounded-4">
                                <form action="{{ route('admin.staff.update', $staff->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header border-bottom py-3">
                                        <h5 class="modal-header-title fw-bold m-0"><i class="bi bi-person-gear text-primary me-2"></i> تعديل بيانات وصلاحيات: {{ $staff->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">الاسم الكامل</label>
                                                <input type="text" name="name" class="form-control" value="{{ $staff->name }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">البريد الإلكتروني</label>
                                                <input type="email" name="email" class="form-control" value="{{ $staff->email }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">رقم الهاتف</label>
                                                <input type="text" name="phone" class="form-control" value="{{ $staff->phone }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">كلمة المرور الجديدة (اتركها فارغة للتجميع بدون تغيير)</label>
                                                <input type="password" name="password" class="form-control" placeholder="••••••••">
                                            </div>
                                        </div>

                                        <h6 class="fw-bold mb-3 text-primary border-bottom pb-2"><i class="bi bi-sliders me-1"></i> تحديد الصلاحيات المتاحة للموظف:</h6>
                                        <div class="row g-3">
                                            @foreach($availablePermissions as $key => $label)
                                                <div class="col-md-6">
                                                    <div class="form-check p-3 border rounded-3 bg-light">
                                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $key }}" id="perm-edit-{{ $staff->id }}-{{ $key }}" {{ in_array($key, $staff->permissions ?? []) ? 'checked' : '' }}>
                                                        <label class="form-check-label small fw-bold ms-2" for="perm-edit-{{ $staff->id }}-{{ $key }}">
                                                            {{ $label }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top py-3">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">حفظ التغييرات</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-2 text-secondary"></i>
                            لا يوجد موظفون مضافون حالياً. يمكنك إضافة أول موظف بالضغط على زر "إضافة موظف جديد".
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('admin.staff.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-header-title fw-bold m-0"><i class="bi bi-person-plus-fill text-primary me-2"></i> إضافة موظف جديد وتحديد صلاحياته</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">الاسم الكامل</label>
                            <input type="text" name="name" class="form-control" placeholder="اسم الموظف" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">البريد الإلكتروني (لتسجيل الدخول)</label>
                            <input type="email" name="email" class="form-control" placeholder="staff@example.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">رقم الهاتف</label>
                            <input type="text" name="phone" class="form-control" placeholder="05xxxxxxxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">كلمة المرور</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 text-primary border-bottom pb-2"><i class="bi bi-sliders me-1"></i> حدد الصلاحيات المصرح للموظف بزيارتها والتحكم بها:</h6>
                    <div class="row g-3">
                        @foreach($availablePermissions as $key => $label)
                            <div class="col-md-6">
                                <div class="form-check p-3 border rounded-3 bg-light">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $key }}" id="perm-add-{{ $key }}">
                                    <label class="form-check-label small fw-bold ms-2" for="perm-add-{{ $key }}">
                                        {{ $label }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-top py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">إضافة الموظف</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
