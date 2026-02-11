@extends('layouts.app')

@section('title', 'الموظفين')
@section('page-title', 'إدارة الموظفين')

@section('content')
    <div class="employees-page">
        <!-- Header Section -->
        <div class="page-header">
            <div class="header-content">
                <h2>قائمة الموظفين</h2>
                <a href="{{ route('employees.create') }}" class="btn-add">
                    <span>➕</span>
                    إضافة موظف جديد
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <!-- فلترة حسب الشهر (كروت) -->
        <div class="month-filters-section">
            <h3 class="filters-title">فلترة حسب الشهر</h3>
            <div class="month-filters-grid">
                <a href="{{ route('employees.index') }}" class="month-filter-card {{ !$selectedMonth ? 'active' : '' }}">
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
                    <a href="{{ route('employees.index', ['month' => $monthValue]) }}" class="month-filter-card {{ $selectedMonth === $monthValue ? 'active' : '' }}">
                        <span class="month-filter-name">{{ $name }} {{ $y }}</span>
                        <span class="month-filter-count">{{ $count }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-cards">
            <div class="stat-card total-card">
                <div class="stat-icon">👥</div>
                <div class="stat-content">
                    <div class="stat-label">إجمالي الموظفين</div>
                    <div class="stat-value">{{ $totalEmployees }}</div>
                </div>
            </div>
            <div class="stat-card active-card">
                <div class="stat-icon">✅</div>
                <div class="stat-content">
                    <div class="stat-label">الموظفين النشطين</div>
                    <div class="stat-value">{{ $activeEmployees }}</div>
                </div>
            </div>
            <div class="stat-card inactive-card">
                <div class="stat-icon">⏸️</div>
                <div class="stat-content">
                    <div class="stat-label">الموظفين غير النشطين</div>
                    <div class="stat-value">{{ $inactiveEmployees }}</div>
                </div>
            </div>
        </div>

        <!-- Bulk: نسخ المحدد إلى شهر -->
        @if($employees->count() > 0)
            <form action="{{ route('employees.move-to-month') }}" method="POST" class="bulk-move-form" id="bulkMoveForm">
                @csrf
                <div class="bulk-bar">
                    <label class="bulk-select-all">
                        <input type="checkbox" id="selectAllEmployees">
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

        <!-- عرض الجدول عند الفلترة بالشهر -->
        @if($selectedMonth && $employees->count() > 0)
            <div class="employees-table-wrap">
                <table class="employees-table">
                    <thead>
                        <tr>
                            <th class="col-check"><span>تحديد</span></th>
                            <th>الاسم</th>
                            <th>الهاتف</th>
                            <th>المشاريع</th>
                            <th>الحالة</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                            <tr>
                                <td class="col-check">
                                    <input type="checkbox" name="ids[]" form="bulkMoveForm" value="{{ $employee->id }}" class="row-select-employee">
                                </td>
                                <td><strong>{{ $employee->name }}</strong></td>
                                <td>{{ $employee->phone ?? '—' }}</td>
                                <td>
                                    @if($employee->projects->count() > 0)
                                        @foreach($employee->projects as $project)
                                            <span class="table-project-badge">📁 {{ $project->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $employee->status }}">
                                        {{ $employee->status === 'active' ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td class="actions-cell">
                                    <a href="{{ route('employees.edit', $employee->id) }}" class="action-btn edit-btn" title="تعديل">✏️ تعديل</a>
                                    <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="delete-form-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الموظف؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete-btn" title="حذف">🗑️ حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @elseif($employees->count() > 0)
            <!-- Employees Cards (عند عرض الكل) -->
            <div class="employees-cards-container">
                <div class="employees-grid">
                    @foreach($employees as $employee)
                        <div class="employee-card">
                            <div class="employee-header">
                                <label class="employee-card-select">
                                    <input type="checkbox" name="ids[]" form="bulkMoveForm" value="{{ $employee->id }}" class="row-select-employee">
                                </label>
                                <div class="employee-avatar">
                                    {{ strtoupper(mb_substr($employee->name, 0, 1)) }}
                                </div>
                                <div class="employee-info">
                                    <h3 class="employee-name">{{ $employee->name }}</h3>
                                    <span class="status-badge status-{{ $employee->status }}">
                                        {{ $employee->status === 'active' ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </div>
                            </div>
                            <div class="employee-details">
                                <div class="detail-item">
                                    <span class="detail-icon">📞</span>
                                    <span class="detail-text">{{ $employee->phone }}</span>
                                </div>
                            </div>
                            <div class="employee-projects">
                                @if($employee->projects->count() > 0)
                                    <div class="projects-section">
                                        <div class="projects-label">المشاريع والخدمات:</div>
                                        <div class="projects-list">
                                            @foreach($employee->projects as $project)
                                                @php
                                                    $serviceTypes = [
                                                        'full_management' => ['name' => 'إدارة كاملة', 'icon' => '🎯'],
                                                        'media_buy' => ['name' => 'ميديا بير', 'icon' => '📢'],
                                                        'design' => ['name' => 'تصميم', 'icon' => '🎨'],
                                                        'videos' => ['name' => 'فيديوهات', 'icon' => '🎬'],
                                                        'publishing' => ['name' => 'نشر', 'icon' => '📱'],
                                                        'programming' => ['name' => 'برمجة', 'icon' => '💻'],
                                                        'part_time' => ['name' => 'دوام جزئي', 'icon' => '⏰']
                                                    ];
                                                    $projectServices = $project->pivot->service_types ? json_decode($project->pivot->service_types, true) : [];
                                                @endphp
                                                <div class="project-item">
                                                    <div class="project-name">
                                                        <span class="project-icon">📁</span>
                                                        <strong>{{ $project->name }}</strong>
                                                    </div>
                                                    @if(count($projectServices) > 0)
                                                        <div class="services-list">
                                                            @foreach($projectServices as $serviceKey)
                                                                @if(isset($serviceTypes[$serviceKey]))
                                                                    <span class="service-badge">
                                                                        {{ $serviceTypes[$serviceKey]['icon'] }} {{ $serviceTypes[$serviceKey]['name'] }}
                                                                    </span>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="services-list">
                                                            <span class="service-badge no-services">لا توجد خدمات</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="projects-section">
                                        <div class="projects-label">المشاريع:</div>
                                        <div class="projects-list">
                                            <span class="project-badge no-projects">لا توجد مشاريع</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="employee-actions">
                                <a href="{{ route('employees.edit', $employee->id) }}" class="action-btn edit-btn" title="تعديل">
                                    <span>✏️</span>
                                    تعديل
                                </a>
                                <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="delete-form" onsubmit="return confirm('هل أنت متأكد من حذف هذا الموظف؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete-btn" title="حذف">
                                        <span>🗑️</span>
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">👥</div>
                <h3>لا يوجد موظفين</h3>
                <p>@if($selectedMonth) لا يوجد موظفين في هذا الشهر. @else ابدأ بإضافة موظف جديد @endif</p>
                <a href="{{ route('employees.create') }}" class="btn-add">إضافة موظف جديد</a>
            </div>
        @endif
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var selectAll = document.getElementById('selectAllEmployees');
        var rowChecks = document.querySelectorAll('.row-select-employee');
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
.employees-page {
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

.employee-card-select {
    flex-shrink: 0;
    margin: 0;
    cursor: pointer;
}
.employee-card-select input { width: 18px; height: 18px; cursor: pointer; }

/* Employees Table (عند الفلترة بالشهر) */
.employees-table-wrap {
    margin-top: 0;
    overflow-x: auto;
    border-radius: 12px;
    border: 1px solid rgba(74, 144, 226, 0.15);
}
.employees-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    font-size: 15px;
}
.employees-table thead {
    background: linear-gradient(135deg, #e8f4f8 0%, #d0e8f2 100%);
    color: #2c3e50;
}
.employees-table th {
    padding: 14px 16px;
    text-align: right;
    font-weight: 600;
    border-bottom: 2px solid rgba(74, 144, 226, 0.2);
}
.employees-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #e8f4f8;
    vertical-align: middle;
}
.employees-table tbody tr:hover { background: #f8fbfd; }
.employees-table .col-check { width: 50px; text-align: center; }
.employees-table .col-check input { width: 18px; height: 18px; cursor: pointer; }
.employees-table .text-muted { color: #94a3b8; font-size: 14px; }
.table-project-badge {
    display: inline-block;
    padding: 4px 10px;
    background: #e8f4f8;
    color: #4a90e2;
    border-radius: 8px;
    font-size: 13px;
    margin-left: 6px;
    margin-bottom: 4px;
}
.employees-table .actions-cell { white-space: nowrap; }
.employees-table .actions-cell .action-btn {
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
.employees-table .actions-cell .edit-btn { background: #e8f4f8; color: #4a90e2; }
.employees-table .actions-cell .edit-btn:hover { background: #d0e8f2; }
.employees-table .actions-cell .delete-btn { background: #ffe5e5; color: #c44d4d; }
.employees-table .actions-cell .delete-btn:hover { background: #ffd0d0; }
.employees-table .delete-form-inline { display: inline; margin: 0; }

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

.salary-card .stat-icon {
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

.salary-card .stat-value {
    color: #f57c00;
    font-size: 28px;
}

.active-card .stat-value {
    color: #2d8659;
}

.inactive-card .stat-value {
    color: #c44d4d;
}

/* Employees Cards Grid */
.employees-cards-container {
    margin-top: 20px;
}

.employees-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
}

.employee-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 15px rgba(74, 144, 226, 0.08);
    border: 1px solid rgba(74, 144, 226, 0.1);
    transition: all 0.3s ease;
}

.employee-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(74, 144, 226, 0.15);
    border-color: rgba(74, 144, 226, 0.2);
}

.employee-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e8f4f8;
}

.employee-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #5ba3d4 0%, #4a90e2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: 700;
    flex-shrink: 0;
}

.employee-info {
    flex: 1;
    min-width: 0;
}

.employee-name {
    font-size: 20px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0 0 8px 0;
    word-break: break-word;
}

.status-badge {
    display: inline-block;
    padding: 6px 16px;
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

.employee-details {
    margin-bottom: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 10px;
}

.detail-icon {
    font-size: 18px;
}

.detail-text {
    font-size: 15px;
    color: #5a6c7d;
    font-weight: 500;
}

.employee-projects {
    margin-bottom: 20px;
}

.projects-section {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.projects-label {
    font-size: 14px;
    color: #6c7a89;
    font-weight: 500;
}

.projects-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.project-item {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 10px;
    border: 1px solid #e8f4f8;
}

.project-name {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    font-size: 15px;
    color: #2c3e50;
}

.project-icon {
    font-size: 18px;
}

.services-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 8px;
}

.service-badge {
    display: inline-block;
    padding: 6px 12px;
    background-color: #e8f4f8;
    color: #4a90e2;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 500;
    white-space: nowrap;
}

.service-badge.no-services {
    background-color: #fff3cd;
    color: #856404;
    font-size: 11px;
}

.project-badge {
    display: inline-block;
    padding: 6px 12px;
    background-color: #e8f4f8;
    color: #4a90e2;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    white-space: nowrap;
}

.project-badge.no-projects {
    background-color: #f8f9fa;
    color: #6c7a89;
}

.employee-actions {
    display: flex;
    gap: 10px;
    padding-top: 20px;
    border-top: 1px solid #e8f4f8;
}

.action-btn {
    flex: 1;
    padding: 10px 16px;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-decoration: none;
}

.edit-btn {
    background: linear-gradient(135deg, #e8f4f8 0%, #d0e8f2 100%);
    color: #4a90e2;
}

.edit-btn:hover {
    background: linear-gradient(135deg, #d0e8f2 0%, #b8d9e8 100%);
    transform: translateY(-2px);
}

.delete-btn {
    background: linear-gradient(135deg, #ffe5e5 0%, #ffd0d0 100%);
    color: #c44d4d;
}

.delete-btn:hover {
    background: linear-gradient(135deg, #ffd0d0 0%, #ffb8b8 100%);
    transform: translateY(-2px);
}

.delete-form {
    flex: 1;
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
    .employees-page {
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

    .employees-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .bulk-bar { flex-direction: column; align-items: stretch; }
    .employees-table { font-size: 14px; }
    .employees-table th, .employees-table td { padding: 10px 12px; }
    .employees-table .actions-cell { white-space: normal; }
    .employees-table .actions-cell .action-btn { margin-bottom: 4px; }

    .employee-actions {
        flex-direction: column;
    }

    .action-btn {
        width: 100%;
    }
}
</style>
@endpush
