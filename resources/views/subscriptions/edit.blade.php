@extends('layouts.app')

@section('title', 'تعديل اشتراك')
@section('page-title', 'تعديل اشتراك')

@section('content')
    <div class="form-container">
        <div class="form-header">
            <h2>تعديل بيانات الاشتراك</h2>
            <a href="{{ route('subscriptions.index') }}" class="btn-back">← العودة للقائمة</a>
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

        <form action="{{ route('subscriptions.update', $subscription->id) }}" method="POST" class="subscription-form">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="site_name">اسم الموقع <span class="required">*</span></label>
                <input 
                    type="text" 
                    id="site_name" 
                    name="site_name" 
                    value="{{ old('site_name', $subscription->site_name) }}" 
                    required
                    placeholder="أدخل اسم الموقع"
                    class="form-input"
                >
            </div>

            <div class="form-group">
                <label for="site_url">الرابط</label>
                <input 
                    type="url" 
                    id="site_url" 
                    name="site_url" 
                    value="{{ old('site_url', $subscription->site_url) }}" 
                    placeholder="https://example.com"
                    class="form-input"
                >
            </div>

            <div class="form-group">
                <label>نوع تسجيل الدخول <span class="required">*</span></label>
                <div class="login-type-cards">
                    <input type="radio" name="login_type" id="user_pass" value="user_pass" class="login-type-input" {{ old('login_type', $subscription->login_type ?? 'user_pass') === 'user_pass' ? 'checked' : '' }} required onchange="toggleLoginFields()">
                    <label for="user_pass" class="login-type-card">
                        <div class="card-icon">🔑</div>
                        <div class="card-title">يوزر وباسورد</div>
                    </label>

                    <input type="radio" name="login_type" id="gmail" value="gmail" class="login-type-input" {{ old('login_type', $subscription->login_type) === 'gmail' ? 'checked' : '' }} onchange="toggleLoginFields()">
                    <label for="gmail" class="login-type-card">
                        <div class="card-icon">📧</div>
                        <div class="card-title">حساب جيميل</div>
                    </label>
                </div>
            </div>

            <div class="form-group" id="username-group" style="display: none;">
                <label for="username">اليوزر <span class="required" id="username-required">*</span></label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    value="{{ old('username', $subscription->login_type === 'gmail' ? '' : $subscription->username) }}" 
                    placeholder="أدخل اسم المستخدم"
                    class="form-input"
                >
            </div>

            <div class="form-group" id="password-group" style="display: none;">
                <label for="password">الباسورد <span class="required" id="password-required">*</span></label>
                <input 
                    type="text" 
                    id="password" 
                    name="password" 
                    value="{{ old('password', $subscription->login_type === 'gmail' ? '' : $subscription->password) }}" 
                    placeholder="أدخل كلمة المرور"
                    class="form-input"
                >
            </div>

            <div class="form-group" id="email-group" style="display: none;">
                <label for="email">الإيميل <span class="required" id="email-required">*</span></label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email', $subscription->login_type === 'gmail' ? $subscription->username : '') }}" 
                    placeholder="أدخل الإيميل"
                    class="form-input"
                >
            </div>

            <div class="form-group">
                <label>نوع الاشتراك <span class="required">*</span></label>
                <div class="ownership-type-cards">
                    <input type="radio" name="subscription_ownership" id="official" value="official" class="ownership-type-input" {{ old('subscription_ownership', $subscription->subscription_ownership ?? 'official') === 'official' ? 'checked' : '' }} required onchange="toggleSharedPhone()">
                    <label for="official" class="ownership-type-card">
                        <div class="card-icon">✅</div>
                        <div class="card-title">اشتراك رسمي</div>
                    </label>

                    <input type="radio" name="subscription_ownership" id="shared" value="shared" class="ownership-type-input" {{ old('subscription_ownership', $subscription->subscription_ownership) === 'shared' ? 'checked' : '' }} onchange="toggleSharedPhone()">
                    <label for="shared" class="ownership-type-card">
                        <div class="card-icon">👥</div>
                        <div class="card-title">اشتراك مع شخص آخر</div>
                    </label>
                </div>
            </div>

            <div class="form-group" id="shared-phone-group" style="display: {{ old('subscription_ownership', $subscription->subscription_ownership ?? 'official') === 'shared' ? 'flex' : 'none' }};">
                <label for="shared_with_phone">رقم الشخص الآخر <span class="required">*</span></label>
                <input 
                    type="text" 
                    id="shared_with_phone" 
                    name="shared_with_phone" 
                    value="{{ old('shared_with_phone', $subscription->shared_with_phone) }}" 
                    placeholder="أدخل رقم التليفون"
                    class="form-input"
                >
            </div>

            <div class="form-group">
                <label>نوع الاشتراك <span class="required">*</span></label>
                <div class="subscription-type-cards">
                    <input type="radio" name="subscription_type" id="monthly" value="monthly" class="subscription-type-input" {{ old('subscription_type', $subscription->subscription_type) === 'monthly' ? 'checked' : '' }} required onchange="toggleRenewalDay()">
                    <label for="monthly" class="subscription-type-card">
                        <div class="card-icon">📅</div>
                        <div class="card-title">شهري</div>
                    </label>

                    <input type="radio" name="subscription_type" id="yearly" value="yearly" class="subscription-type-input" {{ old('subscription_type', $subscription->subscription_type) === 'yearly' ? 'checked' : '' }} onchange="toggleRenewalDay()">
                    <label for="yearly" class="subscription-type-card">
                        <div class="card-icon">📆</div>
                        <div class="card-title">سنوي</div>
                    </label>
                </div>
            </div>

            <div class="form-group" id="renewal-day-group" style="display: {{ old('subscription_type', $subscription->subscription_type) === 'monthly' ? 'flex' : 'none' }};">
                <label for="renewal_day">يوم التجديد <span class="required" id="renewal-day-required">*</span></label>
                <input 
                    type="number" 
                    id="renewal_day" 
                    name="renewal_day" 
                    value="{{ old('renewal_day', $subscription->renewal_day) }}" 
                    min="1"
                    max="31"
                    placeholder="أدخل يوم التجديد (1-31)"
                    class="form-input"
                >
                <small class="form-hint">أدخل رقم اليوم من الشهر (من 1 إلى 31)</small>
            </div>

            <div class="form-group">
                <label>العملة <span class="required">*</span></label>
                <div class="currency-type-cards">
                    <input type="radio" name="currency" id="egp" value="egp" class="currency-type-input" {{ old('currency', $subscription->currency ?? 'egp') === 'egp' ? 'checked' : '' }} required>
                    <label for="egp" class="currency-type-card">
                        <div class="card-icon">🇪🇬</div>
                        <div class="card-title">جنيه مصري</div>
                    </label>

                    <input type="radio" name="currency" id="usd" value="usd" class="currency-type-input" {{ old('currency', $subscription->currency) === 'usd' ? 'checked' : '' }}>
                    <label for="usd" class="currency-type-card">
                        <div class="card-icon">🇺🇸</div>
                        <div class="card-title">دولار</div>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="amount">المبلغ</label>
                <input 
                    type="number" 
                    id="amount" 
                    name="amount" 
                    value="{{ old('amount', $subscription->amount) }}" 
                    step="0.01"
                    min="0"
                    placeholder="أدخل المبلغ"
                    class="form-input"
                >
            </div>

            <div class="form-group">
                <label for="status">الحالة <span class="required">*</span></label>
                <select id="status" name="status" required class="form-input">
                    <option value="active" {{ old('status', $subscription->status ?? 'active') === 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ old('status', $subscription->status ?? 'active') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">تحديث</button>
                <a href="{{ route('subscriptions.index') }}" class="btn-cancel">إلغاء</a>
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

.subscription-form {
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

.login-type-cards,
.ownership-type-cards,
.subscription-type-cards,
.currency-type-cards {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-top: 10px;
}

.login-type-input,
.ownership-type-input,
.subscription-type-input,
.currency-type-input {
    display: none;
}

.login-type-card,
.ownership-type-card,
.subscription-type-card,
.currency-type-card {
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

.login-type-card:hover,
.ownership-type-card:hover,
.subscription-type-card:hover,
.currency-type-card:hover {
    border-color: #4a90e2;
    background: #f0f7fa;
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(74, 144, 226, 0.15);
}

.login-type-input:checked + .login-type-card,
.ownership-type-input:checked + .ownership-type-card,
.subscription-type-input:checked + .subscription-type-card,
.currency-type-input:checked + .currency-type-card {
    border-color: #4a90e2;
    background: linear-gradient(135deg, #e8f4f8 0%, #f0f7fa 100%);
    box-shadow: 0 4px 15px rgba(74, 144, 226, 0.2);
    transform: translateY(-3px);
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

.login-type-input:checked + .login-type-card .card-title,
.ownership-type-input:checked + .ownership-type-card .card-title,
.subscription-type-input:checked + .subscription-type-card .card-title,
.currency-type-input:checked + .currency-type-card .card-title {
    color: #4a90e2;
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

    .login-type-cards,
    .ownership-type-cards,
    .subscription-type-cards,
    .currency-type-cards {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@push('scripts')
<script>
function toggleLoginFields() {
    const loginType = document.querySelector('input[name="login_type"]:checked').value;
    const usernameGroup = document.getElementById('username-group');
    const passwordGroup = document.getElementById('password-group');
    const emailGroup = document.getElementById('email-group');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const emailInput = document.getElementById('email');
    const usernameRequired = document.getElementById('username-required');
    const passwordRequired = document.getElementById('password-required');
    const emailRequired = document.getElementById('email-required');
    
    if (loginType === 'user_pass') {
        // إظهار اليوزر والباسورد
        usernameGroup.style.display = 'flex';
        passwordGroup.style.display = 'flex';
        emailGroup.style.display = 'none';
        
        // جعل اليوزر والباسورد مطلوبين
        usernameInput.setAttribute('required', 'required');
        passwordInput.setAttribute('required', 'required');
        emailInput.removeAttribute('required');
        
        // إظهار علامة مطلوب
        usernameRequired.style.display = 'inline';
        passwordRequired.style.display = 'inline';
        emailRequired.style.display = 'none';
        
        // مسح الإيميل
        emailInput.value = '';
    } else {
        // إظهار الإيميل فقط
        usernameGroup.style.display = 'none';
        passwordGroup.style.display = 'none';
        emailGroup.style.display = 'flex';
        
        // جعل الإيميل مطلوب
        usernameInput.removeAttribute('required');
        passwordInput.removeAttribute('required');
        emailInput.setAttribute('required', 'required');
        
        // إظهار/إخفاء علامة مطلوب
        usernameRequired.style.display = 'none';
        passwordRequired.style.display = 'none';
        emailRequired.style.display = 'inline';
        
        // مسح اليوزر والباسورد
        usernameInput.value = '';
        passwordInput.value = '';
    }
}

function toggleSharedPhone() {
    const ownershipType = document.querySelector('input[name="subscription_ownership"]:checked').value;
    const sharedPhoneGroup = document.getElementById('shared-phone-group');
    const sharedPhoneInput = document.getElementById('shared_with_phone');
    
    if (ownershipType === 'shared') {
        sharedPhoneGroup.style.display = 'flex';
        sharedPhoneInput.setAttribute('required', 'required');
    } else {
        sharedPhoneGroup.style.display = 'none';
        sharedPhoneInput.removeAttribute('required');
        sharedPhoneInput.value = '';
    }
}

function toggleRenewalDay() {
    const subscriptionType = document.querySelector('input[name="subscription_type"]:checked').value;
    const renewalDayGroup = document.getElementById('renewal-day-group');
    const renewalDayInput = document.getElementById('renewal_day');
    const renewalDayRequired = document.getElementById('renewal-day-required');
    
    if (subscriptionType === 'monthly') {
        renewalDayGroup.style.display = 'flex';
        renewalDayInput.setAttribute('required', 'required');
        renewalDayRequired.style.display = 'inline';
    } else {
        renewalDayGroup.style.display = 'none';
        renewalDayInput.removeAttribute('required');
        renewalDayRequired.style.display = 'none';
        renewalDayInput.value = '';
    }
}

// تشغيل الدوال عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    toggleLoginFields();
    toggleSharedPhone();
    toggleRenewalDay();
});
</script>
@endpush

