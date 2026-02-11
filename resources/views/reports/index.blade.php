@extends('layouts.app')

@section('title', 'تقرير الحسابات')
@section('page-title', 'تقرير الحسابات')

@section('content')
    <div class="reports-page">
        <!-- Header Section -->
        <div class="page-header">
            <div class="header-content">
                <h2>تقرير الحسابات الشامل</h2>
                <div class="report-date">
                    <span>الشهر المحدد: {{ \Carbon\Carbon::parse($selectedMonth . '-01')->locale('ar')->translatedFormat('F Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="summary-section">
            <h3 class="section-title">ملخص الحسابات المتوقعة</h3>
            <div class="summary-cards">
                <!-- إجمالي إيرادات المشاريع -->
                <div class="summary-card revenue-card">
                    <div class="card-header">
                        <div class="card-icon">💰</div>
                        <div class="card-title">إجمالي إيرادات المشاريع</div>
                    </div>
                    <div class="card-value">
                        {{ number_format($totalProjectsRevenue, 2) }} ج.م
                    </div>
                    <div class="card-description">
                        مجموع إيرادات جميع المشاريع
                    </div>
                </div>

                <!-- إجمالي المرتبات -->
                <div class="summary-card expenses-card">
                    <div class="card-header">
                        <div class="card-icon">💸</div>
                        <div class="card-title">إجمالي المرتبات</div>
                    </div>
                    <div class="card-value">
                        {{ number_format($totalSalaries, 2) }} ج.م
                    </div>
                    <div class="card-description">
                        مجموع مرتبات جميع الموظفين
                    </div>
                </div>

                <!-- إجمالي الاشتراكات - الدولار -->
                <div class="summary-card subscription-usd-card">
                    <div class="card-header">
                        <div class="card-icon">💵</div>
                        <div class="card-title">إجمالي الاشتراكات (دولار)</div>
                    </div>
                    <div class="card-value">
                        {{ number_format($totalSubscriptionsUSD, 2) }} $
                    </div>
                    <div class="card-description">
                        مجموع الاشتراكات بالدولار
                    </div>
                </div>

                <!-- إجمالي الاشتراكات - المصري -->
                <div class="summary-card subscription-egp-card">
                    <div class="card-header">
                        <div class="card-icon">💳</div>
                        <div class="card-title">إجمالي الاشتراكات (مصري)</div>
                    </div>
                    <div class="card-value">
                        {{ number_format($totalSubscriptionsEGP, 2) }} ج.م
                    </div>
                    <div class="card-description">
                        مجموع الاشتراكات بالجنيه المصري
                    </div>
                </div>
            </div>
        </div>

        <!-- Months Selection -->
        <div class="months-section">
            <div class="months-header">
                <h3 class="section-title">اختر الشهر</h3>
                @if($financialRecords->count() == 0)
                    <button class="btn-create-records" id="createRecordsBtn" data-month="{{ $selectedMonth }}">
                        <span>➕</span>
                        إنشاء السجلات
                    </button>
                @endif
            </div>
            <div class="months-grid">
                @php
                    $months = [
                        '01' => 'يناير', '02' => 'فبراير', '03' => 'مارس', '04' => 'أبريل',
                        '05' => 'مايو', '06' => 'يونيو', '07' => 'يوليو', '08' => 'أغسطس',
                        '09' => 'سبتمبر', '10' => 'أكتوبر', '11' => 'نوفمبر', '12' => 'ديسمبر'
                    ];
                    $currentYear = date('Y');
                    $selectedMonthValue = $selectedMonth;
                @endphp
                @foreach($months as $monthNum => $monthName)
                    @php
                        $monthValue = $currentYear . '-' . $monthNum;
                        $isSelected = $selectedMonthValue === $monthValue;
                    @endphp
                    <a href="{{ route('reports.index', ['month' => $monthValue]) }}" 
                       class="month-card {{ $isSelected ? 'active' : '' }}">
                        <div class="month-number">{{ $monthNum }}</div>
                        <div class="month-name">{{ $monthName }}</div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Actual Summary Section -->
        <div class="actual-summary-section">
            <h3 class="section-title">ملخص الحسابات الفعلي - {{ \Carbon\Carbon::parse($selectedMonth . '-01')->locale('ar')->translatedFormat('F Y') }}</h3>
            <div class="summary-cards">
                <!-- الإيرادات المحصلة فعلاً -->
                <div class="summary-card actual-revenue-card">
                    <div class="card-header">
                        <div class="card-icon">💰</div>
                        <div class="card-title">الإيرادات المحصلة فعلاً</div>
                    </div>
                    <div class="card-value">
                        {{ number_format($recordsRevenue, 2) }} ج.م
                    </div>
                    <div class="card-description">
                        إجمالي الإيرادات المحصلة من السجلات المحاسبية
                    </div>
                </div>

                <!-- المبلغ المصروف فعلاً -->
                <div class="summary-card actual-expenses-card">
                    <div class="card-header">
                        <div class="card-icon">💸</div>
                        <div class="card-title">المبلغ المصروف فعلاً</div>
                    </div>
                    <div class="card-value">
                        {{ number_format($recordsExpenses, 2) }} ج.م
                    </div>
                    <div class="card-description">
                        إجمالي المصروفات الفعلية من السجلات المحاسبية
                    </div>
                </div>

                <!-- الأرباح الفعلية -->
                <div class="summary-card actual-profit-card">
                    <div class="card-header">
                        <div class="card-icon">📈</div>
                        <div class="card-title">الأرباح الفعلية</div>
                    </div>
                    <div class="card-value {{ $recordsNetProfit >= 0 ? 'positive' : 'negative' }}">
                        {{ number_format($recordsNetProfit, 2) }} ج.م
                    </div>
                    <div class="card-description">
                        صافي الربح الفعلي للشهر
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Status Cards Section -->
        <div class="payment-status-section">
            <h3 class="section-title">حالة الدفع - {{ \Carbon\Carbon::parse($selectedMonth . '-01')->locale('ar')->translatedFormat('F Y') }}</h3>
            <div class="summary-cards">
                <!-- الإيرادات المحصلة -->
                <div class="summary-card revenue-paid-card">
                    <div class="card-header">
                        <div class="card-icon">✅</div>
                        <div class="card-title">الإيرادات المحصلة</div>
                    </div>
                    <div class="card-value">
                        {{ number_format($revenuePaid, 2) }} ج.م
                    </div>
                    <div class="card-description">
                        إجمالي الإيرادات التي تم تحصيلها
                    </div>
                </div>

                <!-- الإيرادات المعلقة -->
                <div class="summary-card revenue-pending-card">
                    <div class="card-header">
                        <div class="card-icon">⏳</div>
                        <div class="card-title">الإيرادات المعلقة</div>
                    </div>
                    <div class="card-value">
                        {{ number_format($revenuePending, 2) }} ج.م
                    </div>
                    <div class="card-description">
                        إجمالي الإيرادات المعلقة (لم يتم تحصيلها)
                    </div>
                </div>

                <!-- المصروفات المدفوعة -->
                <div class="summary-card expenses-paid-card">
                    <div class="card-header">
                        <div class="card-icon">💳</div>
                        <div class="card-title">المصروفات المدفوعة</div>
                    </div>
                    <div class="card-value">
                        {{ number_format($expensesPaid, 2) }} ج.م
                    </div>
                    <div class="card-description">
                        إجمالي المصروفات التي تم دفعها
                    </div>
                </div>

                <!-- المصروفات المعلقة -->
                <div class="summary-card expenses-pending-card">
                    <div class="card-header">
                        <div class="card-icon">⚠️</div>
                        <div class="card-title">المصروفات المعلقة</div>
                    </div>
                    <div class="card-value">
                        {{ number_format($expensesPending, 2) }} ج.م
                    </div>
                    <div class="card-description">
                        إجمالي المصروفات المعلقة (لم يتم دفعها)
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Records Section -->
        <div class="transactions-section">
            <div class="section-header">
                <h3 class="section-title">السجلات المحاسبية - {{ \Carbon\Carbon::parse($selectedMonth . '-01')->locale('ar')->translatedFormat('F Y') }}</h3>
                @if($financialRecords->count() > 0)
                    <button class="btn-add-record" id="addRecordBtn" data-month="{{ $selectedMonth }}">
                        <span>➕</span>
                        إضافة سجل جديد
                    </button>
                @endif
            </div>
            @if($financialRecords->count() > 0)
                <div class="transactions-table-container">
                    <table class="transactions-table">
                        <thead>
                            <tr>
                                <th>التاريخ</th>
                                <th>النوع</th>
                                <th>الوصف</th>
                                <th>المبلغ</th>
                                <th>العملة</th>
                                <th>{{ $financialRecords->first()->type == 'revenue' ? 'حالة الدفع' : 'الحالة' }}</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($financialRecords as $record)
                                <tr class="transaction-row transaction-{{ $record->type }}">
                                    <td>{{ $record->record_date->format('Y-m-d') }}</td>
                                    <td>
                                        <span class="transaction-type-badge {{ $record->type == 'revenue' ? 'type-revenue' : 'type-expense' }}">
                                            {{ $record->type == 'revenue' ? 'إيراد' : 'مصروف' }}
                                        </span>
                                    </td>
                                    <td>{{ $record->description }}</td>
                                    <td class="amount-cell">
                                        {{ number_format($record->amount, 2) }}
                                    </td>
                                    <td>
                                        <span class="currency-badge">{{ $record->currency == 'usd' ? '$' : 'ج.م' }}</span>
                                    </td>
                                    <td>
                                        @if($record->type == 'revenue')
                                            <span class="status-badge status-{{ $record->payment_status ?? 'pending' }}">
                                                {{ $record->payment_status == 'paid' ? 'تم التحصيل' : ($record->payment_status == 'pending' ? 'معلق' : 'غير مدفوع') }}
                                            </span>
                                        @else
                                            <span class="status-badge status-{{ $record->status ?? 'pending' }}">
                                                {{ $record->status == 'paid' ? 'تم الدفع' : ($record->status == 'pending' ? 'معلق' : 'غير مدفوع') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            @if($record->type == 'revenue' && ($record->payment_status ?? '') !== 'paid')
                                                <button type="button" class="btn-mark-paid" onclick="markAsPaid({{ $record->id }}, 'revenue')" title="تم التحصيل">
                                                    تم التحصيل
                                                </button>
                                            @elseif($record->type == 'expense' && ($record->status ?? '') !== 'paid')
                                                <button type="button" class="btn-mark-paid" onclick="markAsPaid({{ $record->id }}, 'expense')" title="تم الدفع">
                                                    تم الدفع
                                                </button>
                                            @endif
                                            <button class="btn-edit" onclick="editRecord({{ $record->id }})" title="تعديل">
                                                ✏️
                                            </button>
                                            <button class="btn-delete" onclick="deleteRecord({{ $record->id }})" title="حذف">
                                                🗑️
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="summary-row">
                                <td colspan="3"><strong>الإجمالي</strong></td>
                                <td colspan="4">
                                    <div class="totals-summary">
                                        <div class="total-item">
                                            <span class="total-label">إجمالي الإيرادات:</span>
                                            <span class="total-value revenue">{{ number_format($recordsRevenue, 2) }} ج.م</span>
                                        </div>
                                        <div class="total-item">
                                            <span class="total-label">إجمالي المصروفات:</span>
                                            <span class="total-value expense">{{ number_format($recordsExpenses, 2) }} ج.م</span>
                                        </div>
                                        <div class="total-item">
                                            <span class="total-label">صافي الربح:</span>
                                            <span class="total-value {{ $recordsNetProfit >= 0 ? 'revenue' : 'expense' }}">
                                                {{ number_format($recordsNetProfit, 2) }} ج.م
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @elseif($allRecords->count() > 0)
                <div class="transactions-table-container">
                    <table class="transactions-table">
                        <thead>
                            <tr>
                                <th>التاريخ</th>
                                <th>النوع</th>
                                <th>الوصف</th>
                                <th>المبلغ</th>
                                <th>العملة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allRecords as $record)
                                <tr class="transaction-row transaction-{{ $record['type'] === 'إيراد' ? 'revenue' : 'expense' }}">
                                    <td>{{ $record['date'] }}</td>
                                    <td>
                                        <span class="transaction-type-badge {{ $record['type'] === 'إيراد' ? 'type-revenue' : 'type-expense' }}">
                                            {{ $record['type'] }}
                                        </span>
                                    </td>
                                    <td>{{ $record['description'] }}</td>
                                    <td class="amount-cell">
                                        {{ number_format($record['amount'], 2) }}
                                    </td>
                                    <td>
                                        <span class="currency-badge">{{ $record['currency'] === 'usd' ? '$' : 'ج.م' }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="summary-row">
                                <td colspan="3"><strong>الإجمالي</strong></td>
                                <td colspan="2">
                                    <div class="totals-summary">
                                        <div class="total-item">
                                            <span class="total-label">إجمالي الإيرادات:</span>
                                            <span class="total-value revenue">{{ number_format($totalProjectsRevenue, 2) }} ج.م</span>
                                        </div>
                                        <div class="total-item">
                                            <span class="total-label">إجمالي المصروفات:</span>
                                            <span class="total-value expense">{{ number_format($totalSalaries + $totalSubscriptionsEGP, 2) }} ج.م</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="no-records">
                    <div class="no-records-icon">📋</div>
                    <h4>لا توجد سجلات محاسبية لهذا الشهر</h4>
                    <p>لم يتم إنشاء سجلات محاسبية لشهر {{ \Carbon\Carbon::parse($selectedMonth . '-01')->locale('ar')->translatedFormat('F Y') }}</p>
                    <p style="margin-top: 15px; color: #6c7a89;">اضغط على زر "إنشاء السجلات" أعلاه لإنشاء السجلات المحاسبية من البيانات المتوقعة</p>
                </div>
            @endif
        </div>

        <!-- Details Section -->
        <div class="details-section">
            <h3 class="section-title">ملخص الحسابات</h3>
            <div class="details-grid">
                <div class="detail-box">
                    <div class="detail-label">إجمالي الإيرادات</div>
                    <div class="detail-value revenue">{{ number_format($totalProjectsRevenue, 2) }} ج.م</div>
                </div>
                <div class="detail-box">
                    <div class="detail-label">إجمالي المصروفات</div>
                    <div class="detail-value expense">{{ number_format($totalSalaries + $totalSubscriptionsEGP, 2) }} ج.م</div>
                </div>
                <div class="detail-box">
                    <div class="detail-label">المرتبات</div>
                    <div class="detail-value">{{ number_format($totalSalaries, 2) }} ج.م</div>
                </div>
                <div class="detail-box">
                    <div class="detail-label">الاشتراكات (مصري)</div>
                    <div class="detail-value">{{ number_format($totalSubscriptionsEGP, 2) }} ج.م</div>
                </div>
                <div class="detail-box">
                    <div class="detail-label">الاشتراكات (دولار)</div>
                    <div class="detail-value">{{ number_format($totalSubscriptionsUSD, 2) }} $</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
.reports-page {
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

.report-date {
    color: #6c7a89;
    font-size: 14px;
}

.section-title {
    font-size: 22px;
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 20px;
}

/* Summary Cards */
.summary-section {
    margin-bottom: 40px;
}

.summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.summary-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 15px rgba(74, 144, 226, 0.08);
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.summary-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(74, 144, 226, 0.15);
}

.revenue-card {
    border-color: #4a90e2;
}

.expenses-card {
    border-color: #e74c3c;
}

.subscription-usd-card {
    border-color: #1976d2;
}

.subscription-egp-card {
    border-color: #f57c00;
}

.card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 15px;
}

.card-icon {
    font-size: 32px;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: linear-gradient(135deg, #e8f4f8 0%, #f0f7fa 100%);
}

.revenue-card .card-icon {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
}

.expenses-card .card-icon {
    background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
}

.subscription-usd-card .card-icon {
    background: linear-gradient(135deg, #e3f2fd 0%, #90caf9 100%);
}

.subscription-egp-card .card-icon {
    background: linear-gradient(135deg, #fff4e6 0%, #ffe0b3 100%);
}

.card-title {
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
}

.card-value {
    font-size: 32px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 10px;
}

.revenue-card .card-value {
    color: #4a90e2;
}

.expenses-card .card-value {
    color: #e74c3c;
}

.subscription-usd-card .card-value {
    color: #1976d2;
}

.subscription-egp-card .card-value {
    color: #f57c00;
}

.card-description {
    font-size: 13px;
    color: #6c7a89;
}

/* Actual Summary Section */
.actual-summary-section {
    margin-bottom: 40px;
}

.actual-summary-section .section-title {
    font-size: 24px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 25px;
    text-align: center;
}

.actual-revenue-card {
    border-color: #2d8659;
}

.actual-expenses-card {
    border-color: #e74c3c;
}

.actual-profit-card {
    border-color: #4a90e2;
}

.actual-revenue-card .card-icon {
    background: linear-gradient(135deg, #d4f1e8 0%, #b8e6d1 100%);
}

.actual-expenses-card .card-icon {
    background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
}

.actual-profit-card .card-icon {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
}

.actual-revenue-card .card-value {
    color: #2d8659;
    font-weight: 700;
}

.actual-expenses-card .card-value {
    color: #e74c3c;
    font-weight: 700;
}

.actual-profit-card .card-value.positive {
    color: #2d8659;
    font-weight: 700;
}

.actual-profit-card .card-value.negative {
    color: #e74c3c;
    font-weight: 700;
}

/* Payment Status Section */
.payment-status-section {
    margin-bottom: 40px;
}

.payment-status-section .section-title {
    font-size: 24px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 25px;
    text-align: center;
}

.revenue-paid-card {
    border-color: #2d8659;
}

.revenue-pending-card {
    border-color: #f39c12;
}

.expenses-paid-card {
    border-color: #3498db;
}

.expenses-pending-card {
    border-color: #e74c3c;
}

.revenue-paid-card .card-icon {
    background: linear-gradient(135deg, #d4f1e8 0%, #b8e6d1 100%);
}

.revenue-pending-card .card-icon {
    background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
}

.expenses-paid-card .card-icon {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
}

.expenses-pending-card .card-icon {
    background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
}

.revenue-paid-card .card-value {
    color: #2d8659;
    font-weight: 700;
}

.revenue-pending-card .card-value {
    color: #f39c12;
    font-weight: 700;
}

.expenses-paid-card .card-value {
    color: #3498db;
    font-weight: 700;
}

.expenses-pending-card .card-value {
    color: #e74c3c;
    font-weight: 700;
}

/* Details Section */
.details-section {
    margin-bottom: 30px;
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.detail-box {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #e8f4f8;
}

.detail-label {
    font-size: 14px;
    color: #6c7a89;
    margin-bottom: 10px;
    font-weight: 500;
}

.detail-value {
    font-size: 20px;
    font-weight: 600;
    color: #2c3e50;
}

.detail-value.revenue {
    color: #4a90e2;
}

.detail-value.expense {
    color: #e74c3c;
}

/* Months Section */
.months-section {
    margin-bottom: 40px;
}

.months-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 15px;
}

.month-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    text-decoration: none;
    border: 2px solid #e8f4f8;
    transition: all 0.3s ease;
    cursor: pointer;
}

.month-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(74, 144, 226, 0.15);
    border-color: #4a90e2;
}

.month-card.active {
    background: linear-gradient(135deg, #e8f4f8 0%, #f0f7fa 100%);
    border-color: #4a90e2;
    box-shadow: 0 4px 15px rgba(74, 144, 226, 0.2);
}

.month-number {
    font-size: 24px;
    font-weight: 700;
    color: #4a90e2;
    margin-bottom: 8px;
}

.month-card.active .month-number {
    color: #2c3e50;
}

.month-name {
    font-size: 14px;
    font-weight: 500;
    color: #2c3e50;
}

/* Transactions Section */
.transactions-section {
    margin-bottom: 40px;
}

.transactions-table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(74, 144, 226, 0.08);
}

.transactions-table {
    width: 100%;
    border-collapse: collapse;
}

.transactions-table thead {
    background: linear-gradient(135deg, #e8f4f8 0%, #f0f7fa 100%);
}

.transactions-table th {
    padding: 15px;
    text-align: right;
    font-weight: 600;
    color: #2c3e50;
    border-bottom: 2px solid #e8f4f8;
}

.transactions-table td {
    padding: 15px;
    border-bottom: 1px solid #f0f0f0;
    color: #5a6c7d;
}

.transaction-row:hover {
    background-color: #f8f9fa;
}

.transaction-row.transaction-revenue {
    border-right: 4px solid #4a90e2;
}

.transaction-row.transaction-expense {
    border-right: 4px solid #e74c3c;
}

.transaction-type-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
}

.transaction-type-badge.type-revenue {
    background-color: #d4f1e8;
    color: #2d8659;
}

.transaction-type-badge.type-expense {
    background-color: #ffe5e5;
    color: #c44d4d;
}

.amount-cell {
    font-weight: 600;
    color: #2c3e50;
}

.currency-badge {
    display: inline-block;
    padding: 4px 8px;
    background: #f0f0f0;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
}

.transactions-table tfoot {
    background: #f8f9fa;
}

.summary-row {
    font-weight: 600;
}

.totals-summary {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.total-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.total-label {
    color: #6c7a89;
    font-size: 14px;
}

.total-value {
    font-size: 16px;
    font-weight: 700;
}

.total-value.revenue {
    color: #2d8659;
}

.total-value.expense {
    color: #c44d4d;
}

.no-records {
    text-align: center;
    padding: 60px 20px;
    background: #f8f9fa;
    border-radius: 12px;
    border: 2px dashed #e0e0e0;
}

.no-records-icon {
    font-size: 64px;
    margin-bottom: 20px;
}

.no-records h4 {
    font-size: 20px;
    color: #2c3e50;
    margin-bottom: 10px;
}

.no-records p {
    color: #6c7a89;
    font-size: 14px;
}

@media (max-width: 768px) {
    .reports-page {
        padding: 20px 15px;
    }

    .page-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }

    .summary-cards {
        grid-template-columns: 1fr;
    }

    .actual-summary-section .section-title {
        font-size: 20px;
    }
    }

    .details-grid {
        grid-template-columns: 1fr;
    }

    .months-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }

    .transactions-table-container {
        overflow-x: auto;
    }

    .transactions-table {
        min-width: 600px;
    }

    .transactions-table th,
    .transactions-table td {
        padding: 10px;
        font-size: 13px;
    }
}

/* Section Header */
.months-header,
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.btn-create-records,
.btn-add-record {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: linear-gradient(135deg, #5ba3d4 0%, #4a90e2 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(74, 144, 226, 0.2);
}

.btn-create-records:hover,
.btn-add-record:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(74, 144, 226, 0.3);
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.btn-edit,
.btn-delete {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 18px;
    padding: 6px 10px;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.btn-edit:hover {
    background: #e8f4f8;
    transform: scale(1.1);
}

.btn-delete:hover {
    background: #ffe5e5;
    transform: scale(1.1);
}

.btn-mark-paid {
    padding: 6px 12px;
    font-size: 13px;
    font-weight: 500;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    background: linear-gradient(135deg, #2d8659 0%, #237347 100%);
    color: white;
    transition: all 0.2s ease;
    font-family: 'Cairo', sans-serif;
}

.btn-mark-paid:hover {
    background: linear-gradient(135deg, #237347 0%, #1a5c36 100%);
    transform: scale(1.05);
}

.status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 500;
}

.status-badge.status-paid {
    background-color: #d4f1e8;
    color: #2d8659;
}

.status-badge.status-pending {
    background-color: #fff3cd;
    color: #856404;
}

.status-badge.status-unpaid {
    background-color: #ffe5e5;
    color: #c44d4d;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
}

.modal-content {
    background-color: white;
    margin: 5% auto;
    padding: 0;
    border-radius: 16px;
    width: 90%;
    max-width: 600px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-header {
    padding: 20px 30px;
    border-bottom: 2px solid #e8f4f8;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 24px;
    color: #2c3e50;
}

.close-modal {
    background: none;
    border: none;
    font-size: 28px;
    cursor: pointer;
    color: #6c7a89;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.close-modal:hover {
    background: #f0f0f0;
    color: #2c3e50;
}

.modal-body {
    padding: 30px;
}

.form-group-modal {
    margin-bottom: 20px;
}

.form-group-modal label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #2c3e50;
    font-size: 14px;
}

.form-group-modal input,
.form-group-modal select,
.form-group-modal textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 15px;
    font-family: 'Cairo', sans-serif;
    transition: all 0.3s ease;
}

.form-group-modal input:focus,
.form-group-modal select:focus,
.form-group-modal textarea:focus {
    outline: none;
    border-color: #4a90e2;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
}

.modal-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #e8f4f8;
}

.btn-save {
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
}

.btn-save:hover {
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
    transform: translateY(-2px);
}

.btn-cancel-modal {
    flex: 1;
    padding: 14px;
    background: #e8f4f8;
    color: #5a6c7d;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-cancel-modal:hover {
    background: #d0e8f2;
}

@media (max-width: 768px) {
    .months-header,
    .section-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }

    .modal-content {
        width: 95%;
        margin: 10% auto;
    }

    .modal-body {
        padding: 20px;
    }
}
</style>
@endpush

@push('modals')
<!-- Modal لإضافة/تعديل سجل -->
<div id="recordModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">إضافة سجل جديد</h3>
            <button class="close-modal" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="recordForm">
                <input type="hidden" id="recordId" name="id">
                <input type="hidden" id="recordMonth" name="month" value="{{ $selectedMonth }}">
                
                <div class="form-group-modal">
                    <label for="recordType">النوع <span style="color: #e74c3c;">*</span></label>
                    <select id="recordType" name="type" required>
                        <option value="revenue">إيراد</option>
                        <option value="expense">مصروف</option>
                    </select>
                </div>

                <div class="form-group-modal">
                    <label for="recordDescription">الوصف <span style="color: #e74c3c;">*</span></label>
                    <input type="text" id="recordDescription" name="description" required placeholder="أدخل وصف السجل">
                </div>

                <div class="form-group-modal">
                    <label for="recordAmount">المبلغ <span style="color: #e74c3c;">*</span></label>
                    <input type="number" id="recordAmount" name="amount" step="0.01" min="0" required placeholder="0.00">
                </div>

                <div class="form-group-modal">
                    <label for="recordCurrency">العملة <span style="color: #e74c3c;">*</span></label>
                    <select id="recordCurrency" name="currency" required>
                        <option value="egp">جنيه مصري (ج.م)</option>
                        <option value="usd">دولار ($)</option>
                    </select>
                </div>

                <div class="form-group-modal" id="paymentStatusGroup">
                    <label for="recordPaymentStatus">حالة الدفع</label>
                    <select id="recordPaymentStatus" name="payment_status">
                        <option value="pending">معلق</option>
                        <option value="paid">مدفوع</option>
                        <option value="unpaid">غير مدفوع</option>
                    </select>
                </div>

                <div class="form-group-modal" id="statusGroup" style="display: none;">
                    <label for="recordStatus">الحالة</label>
                    <select id="recordStatus" name="status">
                        <option value="pending">معلق</option>
                        <option value="paid">مدفوع</option>
                        <option value="unpaid">غير مدفوع</option>
                    </select>
                </div>

                <div class="form-group-modal">
                    <label for="recordDate">تاريخ السجل <span style="color: #e74c3c;">*</span></label>
                    <input type="date" id="recordDate" name="record_date" required>
                </div>

                <div class="modal-actions">
                    <button type="submit" class="btn-save">حفظ</button>
                    <button type="button" class="btn-cancel-modal" onclick="closeModal()">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
const selectedMonth = '{{ $selectedMonth }}';

// عرض/إخفاء حقول الحالة حسب النوع
document.getElementById('recordType').addEventListener('change', function() {
    const type = this.value;
    const paymentStatusGroup = document.getElementById('paymentStatusGroup');
    const statusGroup = document.getElementById('statusGroup');
    
    if (type === 'revenue') {
        paymentStatusGroup.style.display = 'block';
        statusGroup.style.display = 'none';
    } else {
        paymentStatusGroup.style.display = 'none';
        statusGroup.style.display = 'block';
    }
});

// إنشاء السجلات
const createRecordsBtn = document.getElementById('createRecordsBtn');
if (createRecordsBtn) {
    createRecordsBtn.addEventListener('click', function() {
        if (confirm('هل أنت متأكد من إنشاء السجلات لهذا الشهر؟')) {
            const month = this.getAttribute('data-month');
            const formData = new FormData();
            formData.append('month', month);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route("reports.create-records") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(function(response) {
                return response.text().then(function(text) {
                    try {
                        var data = JSON.parse(text);
                        if (!response.ok) {
                            throw new Error(data.message || 'حدث خطأ');
                        }
                        return data;
                    } catch (e) {
                        if (!response.ok) {
                            if (response.status === 419) {
                                throw new Error('انتهت الجلسة. يرجى تحديث الصفحة والمحاولة مرة أخرى.');
                            }
                            throw new Error('حدث خطأ من الخادم. يرجى تحديث الصفحة والمحاولة مرة أخرى.');
                        }
                        throw e;
                    }
                });
            })
            .then(function(data) {
                if (data.success) {
                    alert(data.message || 'تم إنشاء السجلات بنجاح');
                    location.reload();
                } else {
                    alert(data.message || 'حدث خطأ أثناء إنشاء السجلات');
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                alert(error.message || 'حدث خطأ أثناء إنشاء السجلات');
            });
        }
    });
}

// فتح Modal لإضافة سجل
document.getElementById('addRecordBtn')?.addEventListener('click', function() {
    document.getElementById('modalTitle').textContent = 'إضافة سجل جديد';
    document.getElementById('recordForm').reset();
    document.getElementById('recordId').value = '';
    document.getElementById('recordMonth').value = this.getAttribute('data-month');
    document.getElementById('recordDate').value = new Date().toISOString().split('T')[0];
    document.getElementById('paymentStatusGroup').style.display = 'block';
    document.getElementById('statusGroup').style.display = 'none';
    document.getElementById('recordModal').style.display = 'block';
});

// تعديل سجل
function editRecord(id) {
    fetch(`/financial-records/${id}`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('فشل في جلب بيانات السجل');
            }
            return response.json();
        })
        .then(record => {
            document.getElementById('modalTitle').textContent = 'تعديل سجل';
            document.getElementById('recordId').value = record.id;
            document.getElementById('recordType').value = record.type;
            document.getElementById('recordDescription').value = record.description;
            document.getElementById('recordAmount').value = record.amount;
            document.getElementById('recordCurrency').value = record.currency;
            // تحويل تاريخ السجل إلى صيغة YYYY-MM-DD
            const recordDate = record.record_date ? record.record_date.split('T')[0] : new Date().toISOString().split('T')[0];
            document.getElementById('recordDate').value = recordDate;
            document.getElementById('recordMonth').value = record.month;
            
            if (record.type === 'revenue') {
                document.getElementById('recordPaymentStatus').value = record.payment_status || 'pending';
                document.getElementById('paymentStatusGroup').style.display = 'block';
                document.getElementById('statusGroup').style.display = 'none';
            } else {
                document.getElementById('recordStatus').value = record.status || 'pending';
                document.getElementById('paymentStatusGroup').style.display = 'none';
                document.getElementById('statusGroup').style.display = 'block';
            }
            
            document.getElementById('recordModal').style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء جلب بيانات السجل: ' + error.message);
        });
}

// تغيير حالة الدفع: تم التحصيل (إيراد) أو تم الدفع (مصروف)
function markAsPaid(id, type) {
    const msg = type === 'revenue' ? 'تأكيد: تم تحصيل هذا الإيراد؟' : 'تأكيد: تم دفع هذا المصروف؟';
    if (!confirm(msg)) return;
    fetch(`/financial-records/${id}/payment-status`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'حدث خطأ');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء تحديث حالة الدفع');
    });
}

// حذف سجل
function deleteRecord(id) {
    if (confirm('هل أنت متأكد من حذف هذا السجل؟')) {
        fetch(`/financial-records/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert('تم حذف السجل بنجاح');
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء حذف السجل');
        });
    }
}

// حفظ السجل
document.getElementById('recordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const id = document.getElementById('recordId').value;
    
    let url = '/financial-records';
    let method = 'POST';
    
    if (id) {
        url = `/financial-records/${id}`;
        method = 'PUT';
        formData.append('_method', 'PUT');
    }
    
    formData.append('_token', '{{ csrf_token() }}');
    
    fetch(url, {
        method: method === 'PUT' ? 'POST' : 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || 'حدث خطأ');
            });
        }
        return response.json();
    })
    .then(data => {
        alert(id ? 'تم تحديث السجل بنجاح' : 'تم إضافة السجل بنجاح');
        closeModal();
        location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء حفظ السجل: ' + error.message);
    });
});

// إغلاق Modal
function closeModal() {
    document.getElementById('recordModal').style.display = 'none';
}

// إغلاق Modal عند الضغط خارجها
window.onclick = function(event) {
    const modal = document.getElementById('recordModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>
@endpush

