@extends('layouts.app')

@section('title', 'الاشتراكات')
@section('page-title', 'إدارة الاشتراكات')

@section('content')
    <div class="subscriptions-page">
        <!-- Header Section -->
        <div class="page-header">
            <div class="header-content">
                <h2>قائمة الاشتراكات</h2>
                <a href="{{ route('subscriptions.create') }}" class="btn-add">
                    <span>➕</span>
                    إضافة اشتراك جديد
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="stats-cards">
            <div class="stat-card total-card">
                <div class="stat-icon">🔔</div>
                <div class="stat-content">
                    <div class="stat-label">إجمالي الاشتراكات</div>
                    <div class="stat-value">{{ $totalSubscriptions }}</div>
                </div>
            </div>
            <div class="stat-card revenue-card-usd">
                <div class="stat-icon">💵</div>
                <div class="stat-content">
                    <div class="stat-label">إجمالي الدولار</div>
                    <div class="stat-value">{{ number_format($totalAmountUSD, 2) }} $</div>
                </div>
            </div>
            <div class="stat-card revenue-card-egp">
                <div class="stat-icon">💰</div>
                <div class="stat-content">
                    <div class="stat-label">إجمالي المصري</div>
                    <div class="stat-value">{{ number_format($totalAmountEGP, 2) }} ج.م</div>
                </div>
            </div>
            <div class="stat-card active-card">
                <div class="stat-icon">✅</div>
                <div class="stat-content">
                    <div class="stat-label">الاشتراكات النشطة</div>
                    <div class="stat-value">{{ $activeSubscriptions }}</div>
                </div>
            </div>
            <div class="stat-card inactive-card">
                <div class="stat-icon">⏸️</div>
                <div class="stat-content">
                    <div class="stat-label">الاشتراكات غير النشطة</div>
                    <div class="stat-value">{{ $inactiveSubscriptions }}</div>
                </div>
            </div>
            <div class="stat-card monthly-card">
                <div class="stat-icon">📅</div>
                <div class="stat-content">
                    <div class="stat-label">اشتراكات شهرية</div>
                    <div class="stat-value">{{ $monthlySubscriptions }}</div>
                </div>
            </div>
            <div class="stat-card yearly-card">
                <div class="stat-icon">📆</div>
                <div class="stat-content">
                    <div class="stat-label">اشتراكات سنوية</div>
                    <div class="stat-value">{{ $yearlySubscriptions }}</div>
                </div>
            </div>
        </div>

        <!-- Subscriptions Table -->
        <div class="subscriptions-table-wrap">
            @if($subscriptions->count() > 0)
                <table class="subscriptions-table">
                    <thead>
                        <tr>
                            <th>الموقع</th>
                            <th>اسم المستخدم</th>
                            <th>كلمة المرور</th>
                            <th>النوع</th>
                            <th>الحالة</th>
                            <th>يوم التجديد</th>
                            <th>المبلغ</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subscriptions as $subscription)
                            <tr>
                                <td>
                                    <strong>{{ $subscription->site_name }}</strong>
                                    @if($subscription->site_url)
                                        <br><a href="{{ $subscription->site_url }}" target="_blank" rel="noopener" class="table-link">{{ Str::limit($subscription->site_url, 35) }}</a>
                                    @endif
                                </td>
                                <td>{{ $subscription->username }}</td>
                                <td>
                                    <span class="password-field" data-password="{{ $subscription->password }}">
                                        <span class="password-mask">••••••••</span>
                                        <button type="button" class="toggle-password" onclick="togglePassword(this)" title="إظهار/إخفاء">👁️</button>
                                    </span>
                                </td>
                                <td>
                                    <span class="type-badge type-{{ $subscription->subscription_type }}">
                                        {{ $subscription->subscription_type === 'monthly' ? 'شهري' : 'سنوي' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $subscription->status ?? 'active' }}">
                                        {{ ($subscription->status ?? 'active') === 'active' ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td>
                                    @if($subscription->subscription_type === 'monthly' && $subscription->renewal_day)
                                        {{ $subscription->renewal_day }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($subscription->amount)
                                        {{ number_format($subscription->amount, 2) }}
                                        {{ $subscription->currency === 'usd' ? '$' : 'ج.م' }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="actions-cell">
                                    <a href="{{ route('subscriptions.edit', $subscription->id) }}" class="action-btn edit-btn" title="تعديل">✏️ تعديل</a>
                                    <form action="{{ route('subscriptions.destroy', $subscription->id) }}" method="POST" class="delete-form-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الاشتراك؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete-btn" title="حذف">🗑️ حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-icon">🔔</div>
                    <h3>لا توجد اشتراكات</h3>
                    <p>ابدأ بإضافة اشتراك جديد</p>
                    <a href="{{ route('subscriptions.create') }}" class="btn-add">إضافة اشتراك جديد</a>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
<style>
.subscriptions-page {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 4px 20px rgba(74, 144, 226, 0.08);
    border: 1px solid rgba(74, 144, 226, 0.1);
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e8f4f8;
}

.page-header h2 {
    font-size: 28px;
    color: #2c3e50;
    font-weight: 600;
    margin: 0;
}

.btn-add {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: linear-gradient(135deg, #5ba3d4 0%, #4a90e2 100%);
    color: white;
    text-decoration: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 500;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(74, 144, 226, 0.2);
}

.btn-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(74, 144, 226, 0.3);
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
}

.btn-add span {
    font-size: 18px;
}

.alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    animation: slideDown 0.3s ease-out;
}

.alert-success {
    background-color: #d4f1e8;
    border: 1px solid #2d8659;
    color: #2d8659;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Statistics Cards */
.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 15px rgba(74, 144, 226, 0.08);
    border: 1px solid rgba(74, 144, 226, 0.1);
    display: flex;
    align-items: center;
    gap: 16px;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(74, 144, 226, 0.15);
}

.stat-icon {
    font-size: 40px;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: linear-gradient(135deg, #e8f4f8 0%, #f0f7fa 100%);
}

.total-card .stat-icon {
    background: linear-gradient(135deg, #e8f4f8 0%, #d0e8f2 100%);
}

.revenue-card-usd .stat-icon {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
}

.revenue-card-egp .stat-icon {
    background: linear-gradient(135deg, #fff4e6 0%, #ffe0b3 100%);
}

.active-card .stat-icon {
    background: linear-gradient(135deg, #d4f1e8 0%, #b8e6d1 100%);
}

.inactive-card .stat-icon {
    background: linear-gradient(135deg, #ffe5e5 0%, #ffd0d0 100%);
}

.monthly-card .stat-icon {
    background: linear-gradient(135deg, #d4f1e8 0%, #b8e6d1 100%);
}

.yearly-card .stat-icon {
    background: linear-gradient(135deg, #ffe5e5 0%, #ffd0d0 100%);
}

.stat-content {
    flex: 1;
}

.stat-label {
    font-size: 14px;
    color: #6c7a89;
    margin-bottom: 8px;
    font-weight: 500;
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: #2c3e50;
}

.total-card .stat-value {
    color: #4a90e2;
}

.revenue-card-usd .stat-value {
    color: #1976d2;
    font-size: 28px;
}

.revenue-card-egp .stat-value {
    color: #f57c00;
    font-size: 28px;
}

.active-card .stat-value {
    color: #2d8659;
}

.inactive-card .stat-value {
    color: #c44d4d;
}

.monthly-card .stat-value {
    color: #2d8659;
}

.yearly-card .stat-value {
    color: #c44d4d;
}

/* Subscriptions Table */
.subscriptions-table-wrap {
    margin-top: 20px;
    overflow-x: auto;
    border-radius: 12px;
    border: 1px solid rgba(74, 144, 226, 0.15);
}

.subscriptions-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    font-size: 15px;
}

.subscriptions-table thead {
    background: linear-gradient(135deg, #e8f4f8 0%, #d0e8f2 100%);
    color: #2c3e50;
}

.subscriptions-table th {
    padding: 14px 16px;
    text-align: right;
    font-weight: 600;
    border-bottom: 2px solid rgba(74, 144, 226, 0.2);
}

.subscriptions-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #e8f4f8;
    vertical-align: middle;
}

.subscriptions-table tbody tr:hover {
    background: #f8fbfd;
}

.subscriptions-table .table-link {
    font-size: 13px;
    color: #4a90e2;
    text-decoration: none;
    margin-top: 4px;
    display: inline-block;
    word-break: break-all;
}

.subscriptions-table .table-link:hover {
    text-decoration: underline;
}

.subscriptions-table .text-muted {
    color: #94a3b8;
    font-size: 14px;
}

.type-badge,
.status-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
}

.status-active {
    background-color: #d4f1e8;
    color: #2d8659;
}

.status-inactive {
    background-color: #ffe5e5;
    color: #c44d4d;
}

.type-monthly {
    background-color: #d4f1e8;
    color: #2d8659;
}

.type-yearly {
    background-color: #ffe5e5;
    color: #c44d4d;
}

.password-field {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.password-mask {
    min-width: 4em;
}

.toggle-password {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 16px;
    padding: 4px;
}

.toggle-password:hover {
    transform: scale(1.1);
}

.subscriptions-table .actions-cell {
    white-space: nowrap;
}

.subscriptions-table .actions-cell .action-btn {
    padding: 8px 14px;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-left: 6px;
}

.subscriptions-table .actions-cell .edit-btn {
    background: #e8f4f8;
    color: #4a90e2;
}

.subscriptions-table .actions-cell .edit-btn:hover {
    background: #d0e8f2;
}

.subscriptions-table .actions-cell .delete-btn {
    background: #ffe5e5;
    color: #c44d4d;
}

.subscriptions-table .actions-cell .delete-btn:hover {
    background: #ffd0d0;
}

.delete-form-inline {
    display: inline;
    margin: 0;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-icon {
    font-size: 64px;
    margin-bottom: 20px;
}

.empty-state h3 {
    font-size: 24px;
    color: #2c3e50;
    margin-bottom: 10px;
}

.empty-state p {
    font-size: 16px;
    color: #6c7a89;
    margin-bottom: 30px;
}

@media (max-width: 768px) {
    .subscriptions-page {
        padding: 20px 15px;
    }

    .page-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }

    .btn-add {
        width: 100%;
        justify-content: center;
    }

    .stats-cards {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .subscriptions-table {
        font-size: 14px;
    }

    .subscriptions-table th,
    .subscriptions-table td {
        padding: 10px 12px;
    }

    .subscriptions-table .actions-cell {
        white-space: normal;
    }

    .subscriptions-table .actions-cell .action-btn {
        margin-bottom: 4px;
    }
}
</style>
@endpush

@push('scripts')
<script>
function togglePassword(button) {
    const passwordField = button.closest('.password-field');
    const passwordMask = passwordField.querySelector('.password-mask');
    const actualPassword = passwordField.getAttribute('data-password');
    
    if (passwordMask.textContent === '••••••••') {
        passwordMask.textContent = actualPassword;
        button.textContent = '🙈';
    } else {
        passwordMask.textContent = '••••••••';
        button.textContent = '👁️';
    }
}
</script>
@endpush

