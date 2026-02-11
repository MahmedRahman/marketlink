@extends('layouts.app')

@section('title', 'المشاريع')
@section('page-title', 'إدارة المشاريع')

@section('content')
    <div class="projects-page">
        <!-- Header Section -->
        <div class="page-header">
            <div class="header-content">
                <h2>قائمة المشاريع</h2>
                <a href="{{ route('projects.create') }}" class="btn-add">
                    <span>➕</span>
                    إضافة مشروع جديد
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $e) {{ $e }} @endforeach
            </div>
        @endif

        <!-- فلترة حسب الشهر (كروت) -->
        <div class="month-filters-section">
            <h3 class="filters-title">فلترة حسب الشهر</h3>
            <div class="month-filters-grid">
                <a href="{{ route('projects.index') }}" class="month-filter-card {{ !$selectedMonth ? 'active' : '' }}">
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
                    <a href="{{ route('projects.index', ['month' => $monthValue]) }}" class="month-filter-card {{ $selectedMonth === $monthValue ? 'active' : '' }}">
                        <span class="month-filter-name">{{ $name }} {{ $y }}</span>
                        <span class="month-filter-count">{{ $count }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-cards">
            <div class="stat-card total-card">
                <div class="stat-icon">📁</div>
                <div class="stat-content">
                    <div class="stat-label">إجمالي المشاريع</div>
                    <div class="stat-value">{{ $totalProjects }}</div>
                </div>
            </div>
            <div class="stat-card active-card">
                <div class="stat-icon">✅</div>
                <div class="stat-content">
                    <div class="stat-label">المشاريع النشطة</div>
                    <div class="stat-value">{{ $activeProjects }}</div>
                </div>
            </div>
            <div class="stat-card inactive-card">
                <div class="stat-icon">⏸️</div>
                <div class="stat-content">
                    <div class="stat-label">المشاريع غير النشطة</div>
                    <div class="stat-value">{{ $inactiveProjects }}</div>
                </div>
            </div>
        </div>

        <!-- Bulk: نسخ المحدد إلى شهر (إنشاء مشاريع جديدة) -->
        @if($projects->count() > 0)
            <form action="{{ route('projects.move-to-month') }}" method="POST" class="bulk-move-form" id="bulkMoveForm">
                @csrf
                <div class="bulk-bar">
                    <label class="bulk-select-all">
                        <input type="checkbox" id="selectAllProjects">
                        <span>اختيار الكل</span>
                    </label>
                    <select name="month" required class="bulk-month-select">
                        @php
                            $monthsList = ['01'=>'يناير','02'=>'فبراير','03'=>'مارس','04'=>'أبريل','05'=>'مايو','06'=>'يونيو','07'=>'يوليو','08'=>'أغسطس','09'=>'سبتمبر','10'=>'أكتوبر','11'=>'نوفمبر','12'=>'ديسمبر'];
                            $currentYear = (int) date('Y');
                        @endphp
                        @for($y = $currentYear - 1; $y <= $currentYear + 1; $y++)
                            @foreach($monthsList as $m => $name)
                                <option value="{{ $y }}-{{ $m }}" {{ ($y . '-' . $m) === date('Y-m') ? 'selected' : '' }}>{{ $name }} {{ $y }}</option>
                            @endforeach
                        @endfor
                    </select>
                    <button type="submit" class="btn-bulk-move" id="btnBulkMove" disabled>نسخ المحدد إلى الشهر (إنشاء جديد)</button>
                </div>
            </form>
        @endif

        <!-- Projects Table -->
        <div class="projects-table-wrap">
            @if($projects->count() > 0)
                <table class="projects-table">
                    <thead>
                        <tr>
                            <th class="col-check"><span>تحديد</span></th>
                            <th>اسم المشروع</th>
                            <th>العميل</th>
                            <th>الشهر</th>
                            <th>الحالة</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projects as $project)
                            <tr>
                                <td class="col-check">
                                    <input type="checkbox" name="ids[]" form="bulkMoveForm" value="{{ $project->id }}" class="row-select">
                                </td>
                                <td><strong>{{ $project->name }}</strong></td>
                                <td>{{ $project->customer->name ?? '—' }}</td>
                                <td>
                                    @if($project->month)
                                        @php
                                            $d = $project->month . '-01';
                                            $t = strtotime($d);
                                            $monthNames = ['01'=>'يناير','02'=>'فبراير','03'=>'مارس','04'=>'أبريل','05'=>'مايو','06'=>'يونيو','07'=>'يوليو','08'=>'أغسطس','09'=>'سبتمبر','10'=>'أكتوبر','11'=>'نوفمبر','12'=>'ديسمبر'];
                                        @endphp
                                        {{ date('Y', $t) }} — {{ $monthNames[date('m', $t)] ?? $project->month }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $project->status }}">
                                        {{ $project->status === 'active' ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td class="actions-cell">
                                    <a href="{{ route('projects.edit', $project->id) }}" class="action-btn edit-btn" title="تعديل">✏️ تعديل</a>
                                    <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="delete-form-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المشروع؟');">
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
                    <div class="empty-icon">📁</div>
                    <h3>لا توجد مشاريع</h3>
                    <p>ابدأ بإضافة مشروع جديد</p>
                    <a href="{{ route('projects.create') }}" class="btn-add">إضافة مشروع جديد</a>
                </div>
            @endif
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var selectAll = document.getElementById('selectAllProjects');
        var rowChecks = document.querySelectorAll('.row-select');
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
.projects-page {
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
.alert-error {
    background-color: #ffe5e5;
    border: 1px solid #c44d4d;
    color: #c44d4d;
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

/* فلترة حسب الشهر - كروت */
.month-filters-section {
    margin-bottom: 28px;
}
.filters-title {
    font-size: 16px;
    color: #2c3e50;
    margin: 0 0 14px 0;
    font-weight: 600;
}
.month-filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 12px;
}
.month-filter-card {
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
.month-filter-card:hover {
    border-color: #4a90e2;
    background: #f8fcff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(74, 144, 226, 0.15);
}
.month-filter-card.active {
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
    border-color: #357abd;
    color: white;
    box-shadow: 0 4px 14px rgba(74, 144, 226, 0.35);
}
.month-filter-name {
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 4px;
}
.month-filter-count {
    font-size: 18px;
    font-weight: 700;
    opacity: 0.9;
}
.month-filter-card.active .month-filter-count {
    opacity: 1;
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

.revenue-card .stat-icon {
    background: linear-gradient(135deg, #fff4e6 0%, #ffe0b3 100%);
}

.active-card .stat-icon {
    background: linear-gradient(135deg, #d4f1e8 0%, #b8e6d1 100%);
}

.inactive-card .stat-icon {
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

.revenue-card .stat-value {
    color: #f57c00;
    font-size: 28px;
}

.active-card .stat-value {
    color: #2d8659;
}

.inactive-card .stat-value {
    color: #c44d4d;
}

/* Bulk move bar */
.bulk-move-form {
    margin-bottom: 20px;
}
.bulk-bar {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    padding: 14px 18px;
    background: #f0f7fa;
    border-radius: 12px;
    border: 1px solid rgba(74, 144, 226, 0.2);
}
.bulk-select-all {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-weight: 500;
    color: #2c3e50;
}
.bulk-select-all input { width: 18px; height: 18px; cursor: pointer; }
.bulk-month-select {
    padding: 8px 14px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 15px;
    min-width: 160px;
}
.btn-bulk-move {
    padding: 10px 20px;
    background: linear-gradient(135deg, #5ba3d4 0%, #4a90e2 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
}
.btn-bulk-move:hover:not(:disabled) {
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
}
.btn-bulk-move:disabled { opacity: 0.6; cursor: not-allowed; }

/* Projects Table */
.projects-table-wrap {
    margin-top: 0;
    overflow-x: auto;
    border-radius: 12px;
    border: 1px solid rgba(74, 144, 226, 0.15);
}
.projects-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    font-size: 15px;
}
.projects-table thead {
    background: linear-gradient(135deg, #e8f4f8 0%, #d0e8f2 100%);
    color: #2c3e50;
}
.projects-table th {
    padding: 14px 16px;
    text-align: right;
    font-weight: 600;
    border-bottom: 2px solid rgba(74, 144, 226, 0.2);
}
.projects-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #e8f4f8;
    vertical-align: middle;
}
.projects-table tbody tr:hover { background: #f8fbfd; }
.projects-table .col-check { width: 50px; text-align: center; }
.projects-table .col-check input { width: 18px; height: 18px; cursor: pointer; }
.projects-table .text-muted { color: #94a3b8; font-size: 14px; }
.status-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
}
.status-active { background-color: #d4f1e8; color: #2d8659; }
.status-inactive { background-color: #ffe5e5; color: #c44d4d; }
.projects-table .actions-cell { white-space: nowrap; }
.projects-table .actions-cell .action-btn {
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
.projects-table .actions-cell .edit-btn { background: #e8f4f8; color: #4a90e2; }
.projects-table .actions-cell .edit-btn:hover { background: #d0e8f2; }
.projects-table .actions-cell .delete-btn { background: #ffe5e5; color: #c44d4d; }
.projects-table .actions-cell .delete-btn:hover { background: #ffd0d0; }
.delete-form-inline { display: inline; margin: 0; }

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
    .projects-page {
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

    .bulk-bar { flex-direction: column; align-items: stretch; }
    .projects-table { font-size: 14px; }
    .projects-table th, .projects-table td { padding: 10px 12px; }
    .projects-table .actions-cell { white-space: normal; }
    .projects-table .actions-cell .action-btn { margin-bottom: 4px; }
}
</style>
@endpush
