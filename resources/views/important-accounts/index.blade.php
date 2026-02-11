@extends('layouts.app')

@section('title', 'حسابات الاشتراكات')
@section('page-title', 'حسابات الاشتراكات')

@section('content')
    <div class="accounts-page-ia subscriptions-page">
        <div class="page-header">
            <div class="header-content">
                <h2>سجل حسابات الاشتراكات</h2>
                <a href="{{ route('important-accounts.create') }}" class="btn-add">
                    <span>➕</span>
                    إضافة حساب جديد
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <!-- فلترة حسب الشهر -->
        <div class="month-filters-section">
            <h3 class="filters-title">فلترة حسب الشهر</h3>
            <div class="month-filters-grid">
                <a href="{{ route('important-accounts.index') }}" class="month-filter-card {{ !$selectedMonth ? 'active' : '' }}">
                    <span class="month-filter-name">الكل</span>
                    <span class="month-filter-count">{{ $allCount }}</span>
                </a>
                @php
                    $monthsList = ['01'=>'يناير','02'=>'فبراير','03'=>'مارس','04'=>'أبريل','05'=>'مايو','06'=>'يونيو','07'=>'يوليو','08'=>'أغسطس','09'=>'سبتمبر','10'=>'أكتوبر','11'=>'نوفمبر','12'=>'ديسمبر'];
                @endphp
                @foreach($monthCounts as $monthValue => $count)
                    @php
                        $t = strtotime($monthValue . '-01');
                        $name = $monthsList[date('m', $t)] ?? $monthValue;
                        $y = date('Y', $t);
                    @endphp
                    <a href="{{ route('important-accounts.index', ['month' => $monthValue]) }}" class="month-filter-card {{ $selectedMonth === $monthValue ? 'active' : '' }}">
                        <span class="month-filter-name">{{ $name }} {{ $y }}</span>
                        <span class="month-filter-count">{{ $count }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="stats-cards">
            <div class="stat-card total-card">
                <div class="stat-icon">🔐</div>
                <div class="stat-content">
                    <div class="stat-label">إجمالي حسابات الاشتراكات</div>
                    <div class="stat-value">{{ $accounts->count() }}</div>
                </div>
            </div>
        </div>

        <!-- نقل إلى شهر -->
        @if($accounts->count() > 0)
            <form action="{{ route('important-accounts.move-to-month') }}" method="POST" class="bulk-move-form" id="bulkMoveForm">
                @csrf
                <div class="bulk-bar">
                    <label class="bulk-select-all">
                        <input type="checkbox" id="selectAllAccounts">
                        <span>اختيار الكل</span>
                    </label>
                    <select name="month" required class="bulk-month-select">
                        @php
                            $monthsListSelect = ['01'=>'يناير','02'=>'فبراير','03'=>'مارس','04'=>'أبريل','05'=>'مايو','06'=>'يونيو','07'=>'يوليو','08'=>'أغسطس','09'=>'سبتمبر','10'=>'أكتوبر','11'=>'نوفمبر','12'=>'ديسمبر'];
                            $currentYear = (int) date('Y');
                        @endphp
                        @for($y = $currentYear - 1; $y <= $currentYear + 1; $y++)
                            @foreach($monthsListSelect as $m => $name)
                                <option value="{{ $y }}-{{ $m }}" {{ ($y . '-' . $m) === date('Y-m') ? 'selected' : '' }}>{{ $name }} {{ $y }}</option>
                            @endforeach
                        @endfor
                    </select>
                    <button type="submit" class="btn-bulk-move" id="btnBulkMove" disabled>نسخ المحدد إلى الشهر (نقل)</button>
                </div>
            </form>
        @endif

        <div class="table-wrap-ia">
            @if($accounts->count() > 0)
                <table class="accounts-table-ia">
                    <thead>
                        <tr>
                            <th class="col-check"><span>تحديد</span></th>
                            <th>الموقع / الرابط</th>
                            <th>الشهر</th>
                            <th>اسم المستخدم</th>
                            <th>كلمة المرور</th>
                            <th>الموظفون المسؤولون</th>
                            <th>ملاحظات</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $account)
                            <tr>
                                <td class="col-check">
                                    <input type="checkbox" name="ids[]" form="bulkMoveForm" value="{{ $account->id }}" class="row-select-account">
                                </td>
                                <td>
                                    <strong>{{ $account->site_name }}</strong>
                                    @if($account->site_url)
                                        <br><a href="{{ $account->site_url }}" target="_blank" rel="noopener" class="detail-link">{{ Str::limit($account->site_url, 40) }}</a>
                                    @endif
                                </td>
                                <td>
                                    @if($account->month)
                                        @php
                                            $d = $account->month . '-01';
                                            $t = strtotime($d);
                                            $monthNames = ['01'=>'يناير','02'=>'فبراير','03'=>'مارس','04'=>'أبريل','05'=>'مايو','06'=>'يونيو','07'=>'يوليو','08'=>'أغسطس','09'=>'سبتمبر','10'=>'أكتوبر','11'=>'نوفمبر','12'=>'ديسمبر'];
                                        @endphp
                                        {{ date('Y', $t) }} — {{ $monthNames[date('m', $t)] ?? $account->month }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $account->username }}</td>
                                <td>
                                    <span class="password-field" data-password="{{ $account->password }}">
                                        <span class="password-mask">••••••••</span>
                                        <button type="button" class="toggle-password" onclick="togglePassword(this)" title="إظهار/إخفاء">👁️</button>
                                    </span>
                                </td>
                                <td>
                                    @if($account->employees->count() > 0)
                                        {{ $account->employees->pluck('name')->join('، ') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($account->notes, 50) }}</td>
                                <td class="actions-cell">
                                    <a href="{{ route('important-accounts.edit', $account) }}" class="action-btn edit-btn" title="تعديل">✏️ تعديل</a>
                                    <form action="{{ route('important-accounts.destroy', $account) }}" method="POST" class="delete-form-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الحساب؟');">
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
                    <div class="empty-icon">🔐</div>
                    <h3>لا توجد حسابات للاشتراكات</h3>
                    <p>سجّل المواقع مع اسم المستخدم وكلمة المرور للوصول السريع</p>
                    <a href="{{ route('important-accounts.create') }}" class="btn-add">إضافة حساب جديد</a>
                </div>
            @endif
        </div>
    </div>

    <script>
    function togglePassword(btn) {
        var wrap = btn.closest('.password-field');
        var mask = wrap.querySelector('.password-mask');
        var password = wrap.getAttribute('data-password');
        if (mask.textContent === '••••••••') {
            mask.textContent = password;
            btn.textContent = '🙈';
        } else {
            mask.textContent = '••••••••';
            btn.textContent = '👁️';
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        var selectAll = document.getElementById('selectAllAccounts');
        var rowChecks = document.querySelectorAll('.row-select-account');
        var btnBulk = document.getElementById('btnBulkMove');
        function updateBulkButton() {
            var any = Array.prototype.some.call(rowChecks, function(c) { return c.checked; });
            if (btnBulk) btnBulk.disabled = !any;
        }
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                rowChecks.forEach(function(c) { c.checked = selectAll.checked; });
                updateBulkButton();
            });
        }
        rowChecks.forEach(function(c) {
            c.addEventListener('change', updateBulkButton);
        });
    });
    </script>
@endsection

@push('styles')
<style>
/* صفحة حسابات الاشتراكات - تنسيق كامل */
.accounts-page-ia {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 4px 20px rgba(74, 144, 226, 0.08);
    border: 1px solid rgba(74, 144, 226, 0.1);
}
.accounts-page-ia .page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e8f4f8;
}
.accounts-page-ia .page-header h2 {
    font-size: 28px;
    color: #2c3e50;
    font-weight: 600;
    margin: 0;
}
.accounts-page-ia .btn-add {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: linear-gradient(135deg, #5ba3d4 0%, #4a90e2 100%);
    color: white !important;
    text-decoration: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 500;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(74, 144, 226, 0.2);
    border: none;
    cursor: pointer;
}
.accounts-page-ia .btn-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(74, 144, 226, 0.3);
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
    color: white !important;
}
.accounts-page-ia .alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}
.accounts-page-ia .alert-success {
    background-color: #d4f1e8;
    border: 1px solid #2d8659;
    color: #2d8659;
}
.accounts-page-ia .alert-error {
    background-color: #ffe5e5;
    border: 1px solid #c44d4d;
    color: #c44d4d;
}

/* فلترة حسب الشهر */
.accounts-page-ia .month-filters-section { margin-bottom: 28px; }
.accounts-page-ia .filters-title { font-size: 16px; color: #2c3e50; margin: 0 0 14px 0; font-weight: 600; }
.accounts-page-ia .month-filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 12px;
}
.accounts-page-ia .month-filter-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 14px 12px;
    background: white;
    border: 2px solid #e8f4f8;
    border-radius: 12px;
    text-decoration: none;
    color: #2c3e50;
    transition: all 0.25s ease;
    min-height: 70px;
}
.accounts-page-ia .month-filter-card:hover {
    border-color: #4a90e2;
    background: #f8fcff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(74, 144, 226, 0.15);
}
.accounts-page-ia .month-filter-card.active {
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
    border-color: #357abd;
    color: white;
    box-shadow: 0 4px 14px rgba(74, 144, 226, 0.35);
}
.accounts-page-ia .month-filter-name { font-size: 13px; font-weight: 600; margin-bottom: 4px; }
.accounts-page-ia .month-filter-count { font-size: 18px; font-weight: 700; opacity: 0.9; }
.accounts-page-ia .month-filter-card.active .month-filter-count { opacity: 1; }

/* شريط النقل */
.accounts-page-ia .bulk-move-form { margin-bottom: 20px; }
.accounts-page-ia .bulk-bar {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    padding: 14px 18px;
    background: #f0f7fa;
    border-radius: 12px;
    border: 1px solid rgba(74, 144, 226, 0.2);
}
.accounts-page-ia .bulk-select-all {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-weight: 500;
    color: #2c3e50;
}
.accounts-page-ia .bulk-select-all input { width: 18px; height: 18px; cursor: pointer; }
.accounts-page-ia .bulk-month-select {
    padding: 8px 14px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 15px;
    min-width: 160px;
}
.accounts-page-ia .btn-bulk-move {
    padding: 10px 20px;
    background: linear-gradient(135deg, #5ba3d4 0%, #4a90e2 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
}
.accounts-page-ia .btn-bulk-move:hover:not(:disabled) {
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
}
.accounts-page-ia .btn-bulk-move:disabled { opacity: 0.6; cursor: not-allowed; }
.accounts-page-ia .col-check { width: 50px; text-align: center; }
.accounts-page-ia .col-check input { width: 18px; height: 18px; cursor: pointer; }

.accounts-page-ia .stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}
.accounts-page-ia .stat-card {
    background: #f8fafc;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(74, 144, 226, 0.06);
    border: 1px solid rgba(74, 144, 226, 0.1);
    display: flex;
    align-items: center;
    gap: 16px;
}
.accounts-page-ia .stat-icon {
    font-size: 40px;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: linear-gradient(135deg, #e8f4f8 0%, #d0e8f2 100%);
}
.accounts-page-ia .stat-label { font-size: 14px; color: #6c7a89; margin-bottom: 8px; font-weight: 500; }
.accounts-page-ia .stat-value { font-size: 32px; font-weight: 700; color: #4a90e2; }

/* جدول الحسابات */
.accounts-page-ia .table-wrap-ia {
    margin-top: 20px;
    overflow-x: auto;
    border-radius: 12px;
    border: 1px solid rgba(74, 144, 226, 0.15);
}
.accounts-page-ia .accounts-table-ia {
    width: 100%;
    border-collapse: collapse;
    background: white;
    font-size: 15px;
}
.accounts-page-ia .accounts-table-ia thead {
    background: linear-gradient(135deg, #e8f4f8 0%, #d0e8f2 100%);
    color: #2c3e50;
}
.accounts-page-ia .accounts-table-ia th {
    padding: 14px 16px;
    text-align: right;
    font-weight: 600;
    border-bottom: 2px solid rgba(74, 144, 226, 0.2);
}
.accounts-page-ia .accounts-table-ia td {
    padding: 14px 16px;
    border-bottom: 1px solid #e8f4f8;
    vertical-align: middle;
}
.accounts-page-ia .accounts-table-ia tbody tr:hover {
    background: #f8fbfd;
}
.accounts-page-ia .detail-link {
    font-size: 13px;
    color: #4a90e2;
    text-decoration: none;
    margin-top: 4px;
    display: inline-block;
    word-break: break-all;
}
.accounts-page-ia .detail-link:hover { text-decoration: underline; }
.accounts-page-ia .password-field { display: inline-flex; align-items: center; gap: 8px; }
.accounts-page-ia .password-mask { min-width: 4em; }
.accounts-page-ia .toggle-password {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 16px;
    padding: 4px;
}
.accounts-page-ia .actions-cell {
    white-space: nowrap;
}
.accounts-page-ia .actions-cell .action-btn {
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
.accounts-page-ia .actions-cell .edit-btn {
    background: #e8f4f8;
    color: #4a90e2;
}
.accounts-page-ia .actions-cell .edit-btn:hover {
    background: #d0e8f2;
}
.accounts-page-ia .actions-cell .delete-btn {
    background: #ffe5e5;
    color: #c44d4d;
}
.accounts-page-ia .actions-cell .delete-btn:hover {
    background: #ffd0d0;
}
.accounts-page-ia .delete-form-inline { display: inline; margin: 0; }
.accounts-page-ia .empty-state { text-align: center; padding: 60px 20px; }
.accounts-page-ia .empty-icon { font-size: 64px; margin-bottom: 20px; }
.accounts-page-ia .empty-state h3 { font-size: 24px; color: #2c3e50; margin-bottom: 10px; }
.accounts-page-ia .empty-state p { font-size: 16px; color: #6c7a89; margin-bottom: 30px; }
.accounts-page-ia .text-muted { color: #94a3b8; font-size: 14px; }
@media (max-width: 768px) {
    .accounts-page-ia { padding: 20px 15px; }
    .accounts-page-ia .page-header { flex-direction: column; gap: 15px; align-items: flex-start; }
    .accounts-page-ia .btn-add { width: 100%; justify-content: center; }
    .accounts-page-ia .bulk-bar { flex-direction: column; align-items: stretch; }
    .accounts-page-ia .accounts-table-ia { font-size: 14px; }
    .accounts-page-ia .accounts-table-ia th,
    .accounts-page-ia .accounts-table-ia td { padding: 10px 12px; }
    .accounts-page-ia .actions-cell { white-space: normal; }
    .accounts-page-ia .actions-cell .action-btn { margin-bottom: 4px; }
}
</style>
@endpush
