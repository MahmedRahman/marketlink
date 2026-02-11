@extends('layouts.app')

@section('title', 'تعديل حساب اشتراك')
@section('page-title', 'تعديل حساب اشتراك')

@section('content')
    <div class="form-container form-container-ia">
        <div class="form-header">
            <h2>تعديل الحساب: {{ $important_account->site_name }}</h2>
            <a href="{{ route('important-accounts.index') }}" class="btn-back">← العودة للقائمة</a>
        </div>

        @if ($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('important-accounts.update', $important_account) }}" method="POST" class="account-form">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="site_name">اسم الموقع / الخدمة <span class="required">*</span></label>
                <input type="text" id="site_name" name="site_name" value="{{ old('site_name', $important_account->site_name) }}" required
                    class="form-input">
            </div>

            <div class="form-group">
                <label for="site_url">رابط الموقع</label>
                <input type="text" id="site_url" name="site_url" value="{{ old('site_url', $important_account->site_url) }}"
                    class="form-input">
            </div>

            <div class="form-group">
                <label for="username">اسم المستخدم / البريد <span class="required">*</span></label>
                <input type="text" id="username" name="username" value="{{ old('username', $important_account->username) }}" required
                    class="form-input">
            </div>

            <div class="form-group">
                <label for="password">كلمة المرور <span class="required">*</span></label>
                <input type="text" id="password" name="password" value="{{ old('password', $important_account->password) }}" required
                    class="form-input">
            </div>

            <div class="form-group">
                <label for="notes">ملاحظات</label>
                <textarea id="notes" name="notes" rows="3" class="form-input">{{ old('notes', $important_account->notes) }}</textarea>
            </div>

            <div class="form-group">
                <label for="month">الشهر</label>
                <select id="month" name="month" class="form-input">
                    @php
                        $monthsList = ['01'=>'يناير','02'=>'فبراير','03'=>'مارس','04'=>'أبريل','05'=>'مايو','06'=>'يونيو','07'=>'يوليو','08'=>'أغسطس','09'=>'سبتمبر','10'=>'أكتوبر','11'=>'نوفمبر','12'=>'ديسمبر'];
                        $currentYear = (int) date('Y');
                        $defaultMonth = old('month', $important_account->month ?? date('Y-m'));
                    @endphp
                    @for($y = $currentYear - 1; $y <= $currentYear + 1; $y++)
                        @foreach($monthsList as $m => $name)
                            <option value="{{ $y }}-{{ $m }}" {{ ($y . '-' . $m) === $defaultMonth ? 'selected' : '' }}>{{ $name }} {{ $y }}</option>
                        @endforeach
                    @endfor
                </select>
            </div>

            <div class="form-group">
                <label>الموظفون المسؤولون</label>
                <p class="field-hint">يمكنك اختيار أكثر من موظف</p>
                <div class="employees-checkboxes">
                    @php $selectedIds = old('employees', $important_account->employees->pluck('id')->toArray()); @endphp
                    @forelse($employees as $emp)
                        <label class="checkbox-label">
                            <input type="checkbox" name="employees[]" value="{{ $emp->id }}" {{ in_array($emp->id, $selectedIds) ? 'checked' : '' }}>
                            <span>{{ $emp->name }}</span>
                            @if($emp->phone)
                                <span class="emp-phone"> — {{ $emp->phone }}</span>
                            @endif
                        </label>
                    @empty
                        <p class="no-employees">لا يوجد موظفون. <a href="{{ route('employees.index') }}">إضافة موظفين</a></p>
                    @endforelse
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit-ia">حفظ التعديلات</button>
                <a href="{{ route('important-accounts.index') }}" class="btn-cancel">إلغاء</a>
            </div>
        </form>
    </div>
@endsection

@push('styles')
<style>
.form-container-ia {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 4px 20px rgba(74, 144, 226, 0.08);
    border: 1px solid rgba(74, 144, 226, 0.1);
    max-width: 560px;
    margin: 0 auto;
}
.form-container-ia .form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e8f4f8;
}
.form-container-ia .form-header h2 {
    font-size: 28px;
    color: #2c3e50;
    font-weight: 600;
    margin: 0;
}
.form-container-ia .btn-back {
    padding: 10px 20px;
    background: #e8f4f8;
    color: #4a90e2;
    text-decoration: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 500;
    transition: all 0.3s ease;
}
.form-container-ia .btn-back:hover { background: #d0e8f2; }
.form-container-ia .alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}
.form-container-ia .alert-error {
    background-color: #ffe5e5;
    border: 1px solid #c44d4d;
    color: #c44d4d;
}
.form-container-ia .alert-error ul { list-style: none; margin: 0; padding: 0; }
.form-container-ia .account-form {
    display: flex;
    flex-direction: column;
    gap: 24px;
}
.form-container-ia .form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.form-container-ia .form-group label {
    font-size: 15px;
    font-weight: 500;
    color: #2c3e50;
}
.form-container-ia .required { color: #e74c3c; }
.form-container-ia .form-input {
    padding: 12px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 15px;
    font-family: 'Cairo', sans-serif;
    transition: all 0.3s ease;
    background-color: #f9f9f9;
    color: #2c3e50;
    width: 100%;
}
.form-container-ia .form-input:focus {
    outline: none;
    border-color: #4a90e2;
    background-color: white;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
}
.form-container-ia textarea.form-input { min-height: 80px; resize: vertical; }
.form-container-ia .form-actions {
    display: flex;
    gap: 15px;
    margin-top: 10px;
    flex-wrap: wrap;
}
.form-container-ia .btn-submit-ia {
    flex: 1;
    min-width: 140px;
    padding: 14px 24px;
    background: linear-gradient(135deg, #5ba3d4 0%, #4a90e2 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(74, 144, 226, 0.2);
}
.form-container-ia .btn-submit-ia:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(74, 144, 226, 0.3);
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
}
.form-container-ia .btn-cancel {
    flex: 1;
    min-width: 120px;
    padding: 14px 24px;
    background: #e8f4f8;
    color: #5a6c7d;
    text-decoration: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 500;
    text-align: center;
    transition: all 0.3s ease;
}
.form-container-ia .btn-cancel:hover { background: #d0e8f2; }
.form-container-ia .field-hint { font-size: 13px; color: #6c7a89; margin: 0 0 10px 0; }
.form-container-ia .employees-checkboxes {
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-height: 220px;
    overflow-y: auto;
    padding: 12px;
    background: #f8fafc;
    border-radius: 10px;
    border: 1px solid #e8f4f8;
}
.form-container-ia .checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    font-size: 15px;
    color: #2c3e50;
}
.form-container-ia .checkbox-label input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; }
.form-container-ia .emp-phone { font-size: 13px; color: #6c7a89; }
.form-container-ia .no-employees { margin: 0; color: #6c7a89; }
.form-container-ia .no-employees a { color: #4a90e2; }
@media (max-width: 768px) {
    .form-container-ia { padding: 20px 15px; }
    .form-container-ia .form-header { flex-direction: column; gap: 15px; align-items: flex-start; }
    .form-container-ia .form-actions { flex-direction: column; }
}
</style>
@endpush
