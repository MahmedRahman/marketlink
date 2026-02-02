@extends('layouts.app')

@section('title', 'حالة الدفع')
@section('page-title', 'حالة الدفع - ' . \Carbon\Carbon::parse($selectedMonth . '-01')->locale('ar')->translatedFormat('F Y'))

@section('content')
    <div class="payment-status-page">
        <div class="page-header">
            <h2>حالة الدفع — {{ \Carbon\Carbon::parse($selectedMonth . '-01')->locale('ar')->translatedFormat('F Y') }}</h2>
            <p class="subtitle">حدّد المطلوب منّا: إيرادات لم تُحصل، ومصروفات لم تُدفع.</p>
        </div>

        {{-- اختيار الشهر --}}
        <div class="months-section">
            <h3 class="section-title">اختر الشهر</h3>
            <div class="months-grid">
                @php
                    $months = [
                        '01' => 'يناير', '02' => 'فبراير', '03' => 'مارس', '04' => 'أبريل',
                        '05' => 'مايو', '06' => 'يونيو', '07' => 'يوليو', '08' => 'أغسطس',
                        '09' => 'سبتمبر', '10' => 'أكتوبر', '11' => 'نوفمبر', '12' => 'ديسمبر'
                    ];
                    $currentYear = substr($selectedMonth, 0, 4);
                @endphp
                @foreach($months as $monthNum => $monthName)
                    @php $monthValue = $currentYear . '-' . $monthNum; @endphp
                    <a href="{{ route('reports.payment-status', ['month' => $monthValue]) }}"
                       class="month-card {{ $selectedMonth === $monthValue ? 'active' : '' }}">
                        <span class="month-number">{{ $monthNum }}</span>
                        <span class="month-name">{{ $monthName }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        @if($financialRecords->isEmpty())
            <div class="no-records">
                <p>لا توجد سجلات محاسبية لشهر {{ \Carbon\Carbon::parse($selectedMonth . '-01')->locale('ar')->translatedFormat('F Y') }}.</p>
                <a href="{{ route('reports.index', ['month' => $selectedMonth]) }}" class="btn-link">الذهاب لتقرير الحسابات لإنشاء السجلات</a>
            </div>
        @else
            {{-- المطلوب منّا: إيرادات لم يتم التحصيل --}}
            <div class="status-section required-section">
                <h3 class="section-title">المطلوب منّا — إيرادات لم يتم التحصيل</h3>
                @if($revenuePending->isEmpty())
                    <p class="empty-message">لا توجد إيرادات معلقة.</p>
                @else
                    <p class="total-line">الإجمالي المطلوب تحصيله: <strong>{{ number_format($sumRevenuePending, 2) }} ج.م</strong></p>
                    <div class="records-list">
                        @foreach($revenuePending as $record)
                            <div class="record-row record-revenue">
                                <span class="record-desc">{{ $record->description }}</span>
                                <span class="record-amount">{{ number_format($record->amount, 2) }} {{ $record->currency === 'usd' ? '$' : 'ج.م' }}</span>
                                <span class="record-date">{{ $record->record_date->format('Y-m-d') }}</span>
                                <button type="button" class="btn-mark-paid" onclick="markAsPaid({{ $record->id }}, 'revenue')">تم التحصيل</button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- المطلوب منّا: مصروفات لم يتم الدفع --}}
            <div class="status-section required-section">
                <h3 class="section-title">المطلوب منّا — مصروفات لم يتم الدفع</h3>
                @if($expensesPending->isEmpty())
                    <p class="empty-message">لا توجد مصروفات معلقة.</p>
                @else
                    <p class="total-line">الإجمالي المطلوب دفعه: <strong>{{ number_format($sumExpensesPending, 2) }} ج.م</strong></p>
                    <div class="records-list">
                        @foreach($expensesPending as $record)
                            <div class="record-row record-expense">
                                <span class="record-desc">{{ $record->description }}</span>
                                <span class="record-amount">{{ number_format($record->amount, 2) }} {{ $record->currency === 'usd' ? '$' : 'ج.م' }}</span>
                                <span class="record-date">{{ $record->record_date->format('Y-m-d') }}</span>
                                <button type="button" class="btn-mark-paid" onclick="markAsPaid({{ $record->id }}, 'expense')">تم الدفع</button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- تم تحصيلها / تم دفعها (للمراجعة) --}}
            <div class="status-section done-section">
                <h3 class="section-title">تم تحصيلها</h3>
                @if($revenuePaid->isEmpty())
                    <p class="empty-message">لا توجد إيرادات محصّلة لهذا الشهر.</p>
                @else
                    <p class="total-line">الإجمالي: <strong>{{ number_format($sumRevenuePaid, 2) }} ج.م</strong></p>
                    <ul class="done-list">
                        @foreach($revenuePaid as $record)
                            <li>{{ $record->description }} — {{ number_format($record->amount, 2) }} {{ $record->currency === 'usd' ? '$' : 'ج.م' }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="status-section done-section">
                <h3 class="section-title">تم دفعها</h3>
                @if($expensesPaid->isEmpty())
                    <p class="empty-message">لا توجد مصروفات مدفوعة لهذا الشهر.</p>
                @else
                    <p class="total-line">الإجمالي: <strong>{{ number_format($sumExpensesPaid, 2) }} ج.م</strong></p>
                    <ul class="done-list">
                        @foreach($expensesPaid as $record)
                            <li>{{ $record->description }} — {{ number_format($record->amount, 2) }} {{ $record->currency === 'usd' ? '$' : 'ج.م' }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        <div class="back-link">
            <a href="{{ route('reports.index', ['month' => $selectedMonth]) }}">← تقرير الحسابات الكامل</a>
        </div>
    </div>
@endsection

@push('styles')
<style>
.payment-status-page { padding: 24px; max-width: 900px; }
.payment-status-page .page-header { margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #e8f4f8; }
.payment-status-page .page-header h2 { margin: 0 0 8px 0; font-size: 24px; color: #2c3e50; }
.payment-status-page .subtitle { margin: 0; color: #6c7a89; font-size: 14px; }
.payment-status-page .section-title { font-size: 18px; color: #2c3e50; margin-bottom: 12px; }
.payment-status-page .months-section { margin-bottom: 28px; }
.payment-status-page .months-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; }
.payment-status-page .month-card { display: flex; flex-direction: column; align-items: center; padding: 12px; border-radius: 10px; border: 2px solid #e8f4f8; text-decoration: none; color: #2c3e50; transition: all 0.2s; }
.payment-status-page .month-card:hover { border-color: #4a90e2; background: #f8fbff; }
.payment-status-page .month-card.active { background: #e8f4f8; border-color: #4a90e2; }
.payment-status-page .month-number { font-weight: 700; color: #4a90e2; }
.payment-status-page .month-name { font-size: 13px; }
.payment-status-page .status-section { margin-bottom: 28px; padding: 20px; background: #f8f9fa; border-radius: 12px; border: 1px solid #e8f4f8; }
.payment-status-page .required-section { border-right: 4px solid #f39c12; }
.payment-status-page .done-section { border-right: 4px solid #2d8659; }
.payment-status-page .total-line { margin-bottom: 12px; color: #2c3e50; }
.payment-status-page .records-list { display: flex; flex-direction: column; gap: 10px; }
.payment-status-page .record-row { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; padding: 12px; background: white; border-radius: 8px; border: 1px solid #e0e0e0; }
.payment-status-page .record-desc { flex: 1; min-width: 180px; }
.payment-status-page .record-amount { font-weight: 600; }
.payment-status-page .record-date { color: #6c7a89; font-size: 13px; }
.payment-status-page .btn-mark-paid { padding: 8px 16px; font-size: 13px; font-weight: 500; border: none; border-radius: 8px; cursor: pointer; background: #2d8659; color: white; transition: all 0.2s; font-family: 'Cairo', sans-serif; }
.payment-status-page .btn-mark-paid:hover { background: #237347; }
.payment-status-page .empty-message { color: #6c7a89; margin: 0; }
.payment-status-page .done-list { margin: 0; padding-right: 20px; }
.payment-status-page .no-records { text-align: center; padding: 40px 20px; background: #f8f9fa; border-radius: 12px; border: 2px dashed #e0e0e0; }
.payment-status-page .btn-link { display: inline-block; margin-top: 12px; color: #4a90e2; font-weight: 500; }
.payment-status-page .back-link { margin-top: 24px; }
.payment-status-page .back-link a { color: #4a90e2; font-weight: 500; }
</style>
@endpush

@push('scripts')
<script>
function markAsPaid(id, type) {
    var msg = type === 'revenue' ? 'تأكيد: تم تحصيل هذا الإيراد؟' : 'تأكيد: تم دفع هذا المصروف؟';
    if (!confirm(msg)) return;
    fetch('/financial-records/' + id + '/payment-status', {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) { alert(data.message); location.reload(); }
        else { alert(data.message || 'حدث خطأ'); }
    })
    .catch(function(e) { console.error(e); alert('حدث خطأ أثناء تحديث حالة الدفع'); });
}
</script>
@endpush
