@extends('layouts.app')

@section('title', 'إضافة عميل جديد')
@section('page-title', 'إضافة عميل جديد')

@section('content')
    <div class="form-container">
        <div class="form-header">
            <h2>إضافة عميل جديد</h2>
            <a href="{{ route('customers.index') }}" class="btn-back">← العودة للقائمة</a>
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

        <form action="{{ route('customers.store') }}" method="POST" class="customer-form">
            @csrf

            <div class="form-group">
                <label for="name">الاسم <span class="required">*</span></label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}" 
                    required
                    placeholder="أدخل اسم العميل"
                    class="form-input"
                >
            </div>

            <div class="form-group">
                <label for="phone">رقم التليفون <span class="required">*</span></label>
                <input 
                    type="text" 
                    id="phone" 
                    name="phone" 
                    value="{{ old('phone') }}" 
                    required
                    placeholder="أدخل رقم التليفون"
                    class="form-input"
                >
            </div>

            <div class="form-group">
                <label for="status">الحالة <span class="required">*</span></label>
                <select id="status" name="status" required class="form-input">
                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">حفظ</button>
                <a href="{{ route('customers.index') }}" class="btn-cancel">إلغاء</a>
            </div>
        </form>
    </div>
@endsection

@push('styles')
<style>
.form-container {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 4px 20px rgba(74, 144, 226, 0.08);
    border: 1px solid rgba(74, 144, 226, 0.1);
    max-width: 600px;
    margin: 0 auto;
}

.form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e8f4f8;
}

.form-header h2 {
    font-size: 28px;
    color: #2c3e50;
    font-weight: 600;
    margin: 0;
}

.btn-back {
    padding: 8px 16px;
    background: #e8f4f8;
    color: #4a90e2;
    text-decoration: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-back:hover {
    background: #d0e8f2;
}

.alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.alert-error {
    background-color: #ffe5e5;
    border: 1px solid #c44d4d;
    color: #c44d4d;
}

.alert-error ul {
    list-style: none;
    margin: 0;
    padding: 0;
}

.alert-error li {
    margin-bottom: 5px;
}

.customer-form {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-size: 15px;
    font-weight: 500;
    color: #2c3e50;
}

.required {
    color: #e74c3c;
}

.form-input {
    padding: 12px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 15px;
    font-family: 'Cairo', sans-serif;
    transition: all 0.3s ease;
    background-color: #f9f9f9;
    color: #2c3e50;
}

.form-input:focus {
    outline: none;
    border-color: #4a90e2;
    background-color: white;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
}

select.form-input {
    cursor: pointer;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 10px;
}

.btn-submit {
    flex: 1;
    padding: 14px;
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

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(74, 144, 226, 0.3);
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
}

.btn-cancel {
    flex: 1;
    padding: 14px;
    background: #e8f4f8;
    color: #5a6c7d;
    text-decoration: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 500;
    text-align: center;
    transition: all 0.3s ease;
}

.btn-cancel:hover {
    background: #d0e8f2;
}

@media (max-width: 768px) {
    .form-container {
        padding: 20px 15px;
    }

    .form-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }

    .form-actions {
        flex-direction: column;
    }
}
</style>
@endpush





