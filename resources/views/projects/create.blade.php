@extends('layouts.app')

@section('title', 'إضافة مشروع جديد')
@section('page-title', 'إضافة مشروع جديد')

@section('content')
    <div class="form-container">
        <div class="form-header">
            <h2>إضافة مشروع جديد</h2>
            <a href="{{ route('projects.index') }}" class="btn-back">← العودة للقائمة</a>
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

        <form action="{{ route('projects.store') }}" method="POST" class="project-form">
            @csrf

            <div class="form-group">
                <label for="customer_id">العميل <span class="required">*</span></label>
                <select id="customer_id" name="customer_id" required class="form-input">
                    <option value="">اختر العميل</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="name">اسم المشروع <span class="required">*</span></label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}" 
                    required
                    placeholder="أدخل اسم المشروع"
                    class="form-input"
                >
            </div>

            <div class="form-group">
                <label>نوع الخدمة <span class="required">*</span></label>
                <div class="service-type-cards" id="serviceTypeCards">
                    @php
                        $selectedTypes = old('service_type', []);
                        $serviceRevenue = old('service_revenue', []);
                    @endphp
                    <div class="service-item">
                        <input type="checkbox" name="service_type[]" id="full_management" value="full_management" class="service-type-input" {{ in_array('full_management', $selectedTypes) ? 'checked' : '' }} onchange="toggleServiceRevenue(this)">
                        <label for="full_management" class="service-type-card">
                            <div class="card-icon">🎯</div>
                            <div class="card-title">إدارة كاملة</div>
                        </label>
                        <input type="number" name="service_revenue[full_management]" value="{{ $serviceRevenue['full_management'] ?? '' }}" step="0.01" min="0" placeholder="الإيراد" class="service-revenue-input" style="display: {{ in_array('full_management', $selectedTypes) ? 'block' : 'none' }};">
                    </div>

                    <div class="service-item">
                        <input type="checkbox" name="service_type[]" id="media_buy" value="media_buy" class="service-type-input" {{ in_array('media_buy', $selectedTypes) ? 'checked' : '' }} onchange="toggleServiceRevenue(this)">
                        <label for="media_buy" class="service-type-card">
                            <div class="card-icon">📢</div>
                            <div class="card-title">ميديا بير</div>
                        </label>
                        <input type="number" name="service_revenue[media_buy]" value="{{ $serviceRevenue['media_buy'] ?? '' }}" step="0.01" min="0" placeholder="الإيراد" class="service-revenue-input" style="display: {{ in_array('media_buy', $selectedTypes) ? 'block' : 'none' }};">
                    </div>

                    <div class="service-item">
                        <input type="checkbox" name="service_type[]" id="design" value="design" class="service-type-input" {{ in_array('design', $selectedTypes) ? 'checked' : '' }} onchange="toggleServiceRevenue(this)">
                        <label for="design" class="service-type-card">
                            <div class="card-icon">🎨</div>
                            <div class="card-title">عمل تصميم</div>
                        </label>
                        <input type="number" name="service_revenue[design]" value="{{ $serviceRevenue['design'] ?? '' }}" step="0.01" min="0" placeholder="الإيراد" class="service-revenue-input" style="display: {{ in_array('design', $selectedTypes) ? 'block' : 'none' }};">
                    </div>

                    <div class="service-item">
                        <input type="checkbox" name="service_type[]" id="videos" value="videos" class="service-type-input" {{ in_array('videos', $selectedTypes) ? 'checked' : '' }} onchange="toggleServiceRevenue(this)">
                        <label for="videos" class="service-type-card">
                            <div class="card-icon">🎬</div>
                            <div class="card-title">فيديوهات</div>
                        </label>
                        <input type="number" name="service_revenue[videos]" value="{{ $serviceRevenue['videos'] ?? '' }}" step="0.01" min="0" placeholder="الإيراد" class="service-revenue-input" style="display: {{ in_array('videos', $selectedTypes) ? 'block' : 'none' }};">
                    </div>

                    <div class="service-item">
                        <input type="checkbox" name="service_type[]" id="publishing" value="publishing" class="service-type-input" {{ in_array('publishing', $selectedTypes) ? 'checked' : '' }} onchange="toggleServiceRevenue(this)">
                        <label for="publishing" class="service-type-card">
                            <div class="card-icon">📱</div>
                            <div class="card-title">نشر</div>
                        </label>
                        <input type="number" name="service_revenue[publishing]" value="{{ $serviceRevenue['publishing'] ?? '' }}" step="0.01" min="0" placeholder="الإيراد" class="service-revenue-input" style="display: {{ in_array('publishing', $selectedTypes) ? 'block' : 'none' }};">
                    </div>

                    <div class="service-item">
                        <input type="checkbox" name="service_type[]" id="programming" value="programming" class="service-type-input" {{ in_array('programming', $selectedTypes) ? 'checked' : '' }} onchange="toggleServiceRevenue(this)">
                        <label for="programming" class="service-type-card">
                            <div class="card-icon">💻</div>
                            <div class="card-title">البرمجة</div>
                        </label>
                        <input type="number" name="service_revenue[programming]" value="{{ $serviceRevenue['programming'] ?? '' }}" step="0.01" min="0" placeholder="الإيراد" class="service-revenue-input" style="display: {{ in_array('programming', $selectedTypes) ? 'block' : 'none' }};">
                    </div>

                    <div class="service-item">
                        <input type="checkbox" name="service_type[]" id="part_time" value="part_time" class="service-type-input" {{ in_array('part_time', $selectedTypes) ? 'checked' : '' }} onchange="toggleServiceRevenue(this)">
                        <label for="part_time" class="service-type-card">
                            <div class="card-icon">⏰</div>
                            <div class="card-title">دوام جزئي</div>
                        </label>
                        <input type="number" name="service_revenue[part_time]" value="{{ $serviceRevenue['part_time'] ?? '' }}" step="0.01" min="0" placeholder="الإيراد" class="service-revenue-input" style="display: {{ in_array('part_time', $selectedTypes) ? 'block' : 'none' }};">
                    </div>
                </div>
                <small class="form-hint">يمكنك اختيار أكثر من خدمة وإدخال إيراد لكل خدمة</small>
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
                <a href="{{ route('projects.index') }}" class="btn-cancel">إلغاء</a>
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

.project-form {
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

.service-type-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 15px;
    margin-top: 10px;
}

.service-item {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.service-type-input {
    display: none;
}

.service-type-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 20px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    min-height: 120px;
}

.service-type-card:hover {
    border-color: #4a90e2;
    background: #f0f7fa;
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(74, 144, 226, 0.15);
}

.service-type-input:checked + .service-type-card {
    border-color: #4a90e2;
    background: linear-gradient(135deg, #e8f4f8 0%, #f0f7fa 100%);
    box-shadow: 0 4px 15px rgba(74, 144, 226, 0.2);
    transform: translateY(-3px);
}

.form-hint {
    display: block;
    margin-top: 8px;
    color: #6c7a89;
    font-size: 13px;
}

.card-icon {
    font-size: 32px;
    margin-bottom: 10px;
}

.card-title {
    font-size: 15px;
    font-weight: 600;
    color: #2c3e50;
}

.service-type-input:checked + .service-type-card .card-title {
    color: #4a90e2;
}

.service-revenue-input {
    padding: 8px 12px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    font-family: 'Cairo', sans-serif;
    transition: all 0.3s ease;
    background-color: #f9f9f9;
    color: #2c3e50;
    width: 100%;
}

.service-revenue-input:focus {
    outline: none;
    border-color: #4a90e2;
    background-color: white;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
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

    .service-type-cards {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .service-type-card {
        padding: 15px;
        min-height: 100px;
    }

    .card-icon {
        font-size: 24px;
        margin-bottom: 8px;
    }

    .card-title {
        font-size: 13px;
    }
}
</style>
@endpush

@push('scripts')
<script>
function toggleServiceRevenue(checkbox) {
    const serviceItem = checkbox.closest('.service-item');
    const revenueInput = serviceItem.querySelector('.service-revenue-input');
    
    if (checkbox.checked) {
        revenueInput.style.display = 'block';
    } else {
        revenueInput.style.display = 'none';
        revenueInput.value = '';
    }
}
</script>
@endpush

