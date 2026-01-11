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

        <!-- Employees Cards -->
        <div class="employees-cards-container">
            @if($employees->count() > 0)
                <div class="employees-grid">
                    @foreach($employees as $employee)
                        <div class="employee-card">
                            <div class="employee-header">
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
            @else
                <div class="empty-state">
                    <div class="empty-icon">👥</div>
                    <h3>لا يوجد موظفين</h3>
                    <p>ابدأ بإضافة موظف جديد</p>
                    <a href="{{ route('employees.create') }}" class="btn-add">إضافة موظف جديد</a>
                </div>
            @endif
        </div>
    </div>
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
    gap: 16px;
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

    .employee-actions {
        flex-direction: column;
    }

    .action-btn {
        width: 100%;
    }
}
</style>
@endpush
