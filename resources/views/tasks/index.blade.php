@extends('layouts.app')

@section('content')
<div class="tasks-container">
    <!-- Month Selection Screen -->
    <div class="month-selection-screen" id="monthSelectionScreen" style="{{ $selectedMonth ? 'display: none;' : 'display: flex;' }}">
        <div class="month-selection-card">
            <h2 class="month-selection-title">اختر الشهر لعرض المهام</h2>
            <div class="month-selection-input-wrapper">
                <label for="monthSelectMain" class="month-selection-label">الشهر:</label>
                <input type="month" id="monthSelectMain" value="{{ $selectedMonth ?: date('Y-m') }}" class="month-selection-input">
                <button class="btn-load-tasks" id="loadTasksBtn">
                    <span>📅</span>
                    عرض المهام
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content (Hidden until month is selected) -->
    <div class="tasks-main-content" id="tasksMainContent" style="{{ $selectedMonth ? 'display: block;' : 'display: none;' }}">
        <!-- Header -->
        <div class="tasks-header">
            <div class="header-left">
                <h1 class="tasks-title">لوحة المهام</h1>
                <div class="current-month-display">
                    <span class="month-label">الشهر الحالي:</span>
                    <span class="month-value">{{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->locale('ar')->translatedFormat('F Y') }}</span>
                </div>
            </div>
            <div class="header-right">
                <div class="view-toggle">
                    <button class="view-toggle-btn active" id="viewByEmployee" data-view="employee" title="عرض حسب الموظف">
                        <span class="toggle-icon">👤</span>
                        <span class="toggle-text">حسب الموظف</span>
                    </button>
                    <button class="view-toggle-btn" id="viewByProject" data-view="project" title="عرض حسب المشروع">
                        <span class="toggle-icon">📁</span>
                        <span class="toggle-text">حسب المشروع</span>
                    </button>
                </div>
                <div class="month-selector">
                    <label for="monthSelect">تغيير الشهر:</label>
                    <input type="month" id="monthSelect" value="{{ $selectedMonth }}" class="month-input">
                </div>
            </div>
        </div>

        <!-- Add Task Buttons -->
        <div class="add-task-section">
            <button class="btn-add-task" id="addTaskBtn">
                <span>➕</span>
                إضافة مهمة جديدة
            </button>
        </div>

    <!-- Tasks List (Rows) - By Employee -->
    <div class="tasks-rows-container view-employee" id="tasksRowsContainer">
        <!-- قسم المهام بدون موظف -->
        @php
            $noEmployeeTasks = $tasksByEmployee->get(null, collect());
        @endphp
        @if($noEmployeeTasks->count() > 0)
        <div class="status-section" data-employee-id="" data-section-type="no-employee">
            <div class="status-header">
                <span class="drag-handle">☰</span>
                <h3 class="status-title">🚫 بدون موظف</h3>
                <span class="task-count" id="count-no-employee">{{ $noEmployeeTasks->count() }}</span>
            </div>
            <div class="tasks-rows-list" id="tasks-no-employee" data-employee-id="">
                @foreach($noEmployeeTasks as $task)
                @include('tasks.partials.task-row', ['task' => $task])
                @endforeach
            </div>
        </div>
        @endif
        
        @foreach($employees as $employee)
        @php
            $employeeTasks = $tasksByEmployee->get($employee->id, collect());
        @endphp
        <div class="status-section" data-employee-id="{{ $employee->id }}" data-section-type="employee" draggable="true">
            <div class="status-header">
                <span class="drag-handle">☰</span>
                <h3 class="status-title">
                    👤 {{ $employee->name }}
                    @if($employee->phone)
                    <a href="{{ route('tasks.employee', ['phone' => rawurlencode($employee->phone)]) }}" target="_blank" class="employee-link" title="رابط المهام الخاصة بالموظف">
                        🔗
                    </a>
                    @endif
                </h3>
                <span class="task-count" id="count-{{ $employee->id }}">{{ $employeeTasks->count() }}</span>
            </div>
            <div class="tasks-rows-list" id="tasks-{{ $employee->id }}" data-employee-id="{{ $employee->id }}">
                @foreach($employeeTasks as $task)
                @include('tasks.partials.task-row', ['task' => $task])
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <!-- Tasks List (Rows) - By Project -->
    <div class="tasks-rows-container view-project" id="tasksRowsContainerByProject" style="display: none;">
        <!-- قسم المهام بدون مشروع -->
        @php
            $noProjectTasks = $tasksByProject->get(null, collect());
        @endphp
        @if($noProjectTasks->count() > 0)
        <div class="status-section" data-project-id="" data-section-type="no-project">
            <div class="status-header">
                <span class="drag-handle">☰</span>
                <h3 class="status-title">🚫 بدون مشروع</h3>
                <span class="task-count" id="count-no-project">{{ $noProjectTasks->count() }}</span>
            </div>
            <div class="tasks-rows-list" id="tasks-no-project" data-project-id="">
                @foreach($noProjectTasks as $task)
                @include('tasks.partials.task-row', ['task' => $task])
                    <div class="task-row-content">
                        <div class="task-row-main">
                            <div class="task-row-left">
                                <span class="task-priority priority-{{ $task->priority }}"></span>
                                <div class="task-row-info">
                                    <div class="task-title-row">
                                        <h4 class="task-title">{{ $task->title }}</h4>
                                    </div>
                                    @if($task->description)
                                    <p class="task-description">{{ $task->description }}</p>
                                    @endif
                                    @if($task->task_types && count($task->task_types) > 0)
                                    <div class="task-types">
                                        @foreach($task->task_types as $type)
                                        <span class="task-type-badge">
                                            @if($type == 'كتابة') ✍️
                                            @elseif($type == 'فيديو') 🎥
                                            @elseif($type == 'إعلان') 📢
                                            @elseif($type == 'تقرير') 📊
                                            @elseif($type == 'إعلان آخر') 📰
                                            @elseif($type == 'تصميم') 🎨
                                            @elseif($type == 'نشر') 📤
                                            @endif
                                            {{ $type }}
                                        </span>
                                        @endforeach
                                    </div>
                                    @endif
                                    @if($task->project)
                                    <div class="task-project-container">
                                        <span class="task-project-label">المشروع:</span>
                                        <span class="task-project">{{ $task->project->name }}</span>
                                    </div>
                                    @endif
                                    <div class="task-meta">
                                        @if($task->employee)
                                        <span class="task-employee">👤 {{ $task->employee->name }}</span>
                                        @endif
                                        @if($task->due_date)
                                        @php
                                            $dueDate = \Carbon\Carbon::parse($task->due_date);
                                            $dayOfMonth = $dueDate->day;
                                            $weekNum = min(max(ceil($dayOfMonth / 7), 1), 4);
                                            $dayOfWeek = $dueDate->dayOfWeek;
                                            $weekNames = [1 => 'الأول', 2 => 'الثاني', 3 => 'الثالث', 4 => 'الرابع'];
                                            $dayNames = [0 => 'الأحد', 1 => 'الإثنين', 2 => 'الثلاثاء', 3 => 'الأربعاء', 4 => 'الخميس', 5 => 'الجمعة', 6 => 'السبت'];
                                        @endphp
                                        <span class="task-due-date {{ $task->due_date->isPast() && $task->status !== 'done' ? 'overdue' : '' }}">
                                            📅 الأسبوع {{ $weekNames[$weekNum] }} - {{ $dayNames[$dayOfWeek] }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="task-row-actions">
                                <button class="btn-edit-task" data-task-id="{{ $task->id }}" title="تعديل">
                                    ✏️
                                </button>
                                <button class="btn-delete-task" data-task-id="{{ $task->id }}" title="حذف">
                                    🗑️
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        
        @foreach($projects as $project)
        @php
            $projectTasks = $tasksByProject->get($project->id, collect());
        @endphp
        @if($projectTasks->count() > 0)
        <div class="status-section" data-project-id="{{ $project->id }}" data-section-type="project" draggable="true">
            <div class="status-header">
                <span class="drag-handle">☰</span>
                <h3 class="status-title">📁 {{ $project->name }}</h3>
                <span class="task-count" id="count-project-{{ $project->id }}">{{ $projectTasks->count() }}</span>
            </div>
            <div class="tasks-rows-list" id="tasks-project-{{ $project->id }}" data-project-id="{{ $project->id }}">
                @foreach($projectTasks as $task)
                <div class="task-row" data-task-id="{{ $task->id }}" draggable="true">
                    <div class="task-row-content">
                        <div class="task-row-main">
                            <div class="task-row-left">
                                <span class="task-priority priority-{{ $task->priority }}"></span>
                                <div class="task-row-info">
                                    <div class="task-title-row">
                                        <h4 class="task-title">{{ $task->title }}</h4>
                                    </div>
                                    @if($task->description)
                                    <p class="task-description">{{ $task->description }}</p>
                                    @endif
                                    @if($task->task_types && count($task->task_types) > 0)
                                    <div class="task-types">
                                        @foreach($task->task_types as $type)
                                        <span class="task-type-badge">
                                            @if($type == 'كتابة') ✍️
                                            @elseif($type == 'فيديو') 🎥
                                            @elseif($type == 'إعلان') 📢
                                            @elseif($type == 'تقرير') 📊
                                            @elseif($type == 'إعلان آخر') 📰
                                            @elseif($type == 'تصميم') 🎨
                                            @elseif($type == 'نشر') 📤
                                            @endif
                                            {{ $type }}
                                        </span>
                                        @endforeach
                                    </div>
                                    @endif
                                    @if($task->project)
                                    <div class="task-project-container">
                                        <span class="task-project-label">المشروع:</span>
                                        <span class="task-project">{{ $task->project->name }}</span>
                                    </div>
                                    @endif
                                    <div class="task-meta">
                                        @if($task->employee)
                                        <span class="task-employee">👤 {{ $task->employee->name }}</span>
                                        @endif
                                        @if($task->due_date)
                                        @php
                                            $dueDate = \Carbon\Carbon::parse($task->due_date);
                                            $dayOfMonth = $dueDate->day;
                                            $weekNum = min(max(ceil($dayOfMonth / 7), 1), 4);
                                            $dayOfWeek = $dueDate->dayOfWeek;
                                            $weekNames = [1 => 'الأول', 2 => 'الثاني', 3 => 'الثالث', 4 => 'الرابع'];
                                            $dayNames = [0 => 'الأحد', 1 => 'الإثنين', 2 => 'الثلاثاء', 3 => 'الأربعاء', 4 => 'الخميس', 5 => 'الجمعة', 6 => 'السبت'];
                                        @endphp
                                        <span class="task-due-date {{ $task->due_date->isPast() && $task->status !== 'done' ? 'overdue' : '' }}">
                                            📅 الأسبوع {{ $weekNames[$weekNum] }} - {{ $dayNames[$dayOfWeek] }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="task-row-actions">
                                <button class="btn-edit-task" data-task-id="{{ $task->id }}" title="تعديل">
                                    ✏️
                                </button>
                                <button class="btn-delete-task" data-task-id="{{ $task->id }}" title="حذف">
                                    🗑️
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endforeach
    </div>

</div>

<!-- Add/Edit Task Modal -->
<div class="modal" id="taskModal">
    <div class="modal-content task-modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">إضافة مهمة جديدة</h2>
            <button class="close" id="closeModal">&times;</button>
        </div>
        <form id="taskForm">
            <input type="hidden" id="taskId" name="task_id">
            <input type="hidden" id="parentId" name="parent_id">
            <div class="form-group" id="employeeFormGroup">
                <label>الموظف (اختياري)</label>
                <div class="employee-cards-container">
                    <div class="employee-card-option" data-employee-id="">
                        <input type="radio" name="employee_id" id="employee-none" value="" checked>
                        <label for="employee-none" class="employee-card-label">
                            <span class="employee-card-icon">🚫</span>
                            <span class="employee-card-text">بدون موظف</span>
                        </label>
                    </div>
                    @foreach($employees as $employee)
                    <div class="employee-card-option" data-employee-id="{{ $employee->id }}">
                        <input type="radio" name="employee_id" id="employee-{{ $employee->id }}" value="{{ $employee->id }}">
                        <label for="employee-{{ $employee->id }}" class="employee-card-label">
                            <span class="employee-card-icon">👤</span>
                            <span class="employee-card-text">{{ $employee->name }}</span>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="form-group" id="projectFormGroup">
                <label>المشروع (اختياري)</label>
                <div class="project-cards-container">
                    <div class="project-card-option" data-project-id="">
                        <input type="radio" name="project_id" id="project-none" value="" checked>
                        <label for="project-none" class="project-card-label">
                            <span class="project-card-icon">🚫</span>
                            <span class="project-card-text">بدون مشروع</span>
                        </label>
                    </div>
                    @foreach($projects as $project)
                    <div class="project-card-option" data-project-id="{{ $project->id }}">
                        <input type="radio" name="project_id" id="project-{{ $project->id }}" value="{{ $project->id }}">
                        <label for="project-{{ $project->id }}" class="project-card-label">
                            <span class="project-card-icon">📁</span>
                            <span class="project-card-text">{{ $project->name }}</span>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="form-group">
                <label for="taskTitle">عنوان المهمة *</label>
                <input type="text" id="taskTitle" name="title" required placeholder="أدخل عنوان المهمة">
            </div>
            <div class="form-group">
                <label for="taskDescription">الوصف</label>
                <textarea id="taskDescription" name="description" rows="4" placeholder="أدخل وصف المهمة"></textarea>
            </div>
            <div class="form-group">
                <label>نوع المهمة (اختياري - يمكن اختيار أكثر من نوع)</label>
                <div class="task-types-container">
                    <div class="task-type-card" data-task-type="كتابة">
                        <input type="checkbox" name="task_types[]" id="task-type-writing" value="كتابة">
                        <label for="task-type-writing" class="task-type-label">
                            <span class="task-type-icon">✍️</span>
                            <span class="task-type-text">كتابة</span>
                        </label>
                    </div>
                    <div class="task-type-card" data-task-type="فيديو">
                        <input type="checkbox" name="task_types[]" id="task-type-video" value="فيديو">
                        <label for="task-type-video" class="task-type-label">
                            <span class="task-type-icon">🎥</span>
                            <span class="task-type-text">فيديو</span>
                        </label>
                    </div>
                    <div class="task-type-card" data-task-type="إعلان">
                        <input type="checkbox" name="task_types[]" id="task-type-ad" value="إعلان">
                        <label for="task-type-ad" class="task-type-label">
                            <span class="task-type-icon">📢</span>
                            <span class="task-type-text">إعلان</span>
                        </label>
                    </div>
                    <div class="task-type-card" data-task-type="تقرير">
                        <input type="checkbox" name="task_types[]" id="task-type-report" value="تقرير">
                        <label for="task-type-report" class="task-type-label">
                            <span class="task-type-icon">📊</span>
                            <span class="task-type-text">تقرير</span>
                        </label>
                    </div>
                    <div class="task-type-card" data-task-type="إعلان آخر">
                        <input type="checkbox" name="task_types[]" id="task-type-other-ad" value="إعلان آخر">
                        <label for="task-type-other-ad" class="task-type-label">
                            <span class="task-type-icon">📰</span>
                            <span class="task-type-text">إعلان آخر</span>
                        </label>
                    </div>
                    <div class="task-type-card" data-task-type="تصميم">
                        <input type="checkbox" name="task_types[]" id="task-type-design" value="تصميم">
                        <label for="task-type-design" class="task-type-label">
                            <span class="task-type-icon">🎨</span>
                            <span class="task-type-text">تصميم</span>
                        </label>
                    </div>
                    <div class="task-type-card" data-task-type="نشر">
                        <input type="checkbox" name="task_types[]" id="task-type-publish" value="نشر">
                        <label for="task-type-publish" class="task-type-label">
                            <span class="task-type-icon">📤</span>
                            <span class="task-type-text">نشر</span>
                        </label>
                    </div>
                </div>
                <input type="hidden" id="taskTypesHidden" name="task_types_json">
            </div>
            <div class="form-group" id="monthFormGroup">
                    <label for="taskMonth">الشهر *</label>
                    <input type="month" id="taskMonth" name="month" value="{{ $selectedMonth }}" required>
                </div>
            <div class="form-group" id="weekFormGroup">
                <label>الأسبوع</label>
                <div class="week-cards-container">
                    <div class="week-card-option" data-week-value="">
                        <input type="radio" name="due_week" id="week-none" value="">
                        <label for="week-none" class="week-card-label">
                            <span class="week-card-icon">🚫</span>
                            <span class="week-card-text">بدون أسبوع</span>
                        </label>
                    </div>
                    <div class="week-card-option" data-week-value="1">
                        <input type="radio" name="due_week" id="week-1" value="1">
                        <label for="week-1" class="week-card-label">
                            <span class="week-card-icon">1️⃣</span>
                            <span class="week-card-text">الأسبوع الأول</span>
                        </label>
                    </div>
                    <div class="week-card-option" data-week-value="2">
                        <input type="radio" name="due_week" id="week-2" value="2">
                        <label for="week-2" class="week-card-label">
                            <span class="week-card-icon">2️⃣</span>
                            <span class="week-card-text">الأسبوع الثاني</span>
                        </label>
                    </div>
                    <div class="week-card-option" data-week-value="3">
                        <input type="radio" name="due_week" id="week-3" value="3">
                        <label for="week-3" class="week-card-label">
                            <span class="week-card-icon">3️⃣</span>
                            <span class="week-card-text">الأسبوع الثالث</span>
                        </label>
                    </div>
                    <div class="week-card-option" data-week-value="4">
                        <input type="radio" name="due_week" id="week-4" value="4">
                        <label for="week-4" class="week-card-label">
                            <span class="week-card-icon">4️⃣</span>
                            <span class="week-card-text">الأسبوع الرابع</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group" id="daySelectionGroup" style="display: none;">
                <label>اليوم</label>
                <div class="day-cards-container">
                    <div class="day-card-option" data-day-value="0">
                        <input type="radio" name="due_day" id="day-0" value="0">
                        <label for="day-0" class="day-card-label">
                            <span class="day-card-icon">📅</span>
                            <span class="day-card-text">الأحد</span>
                        </label>
                    </div>
                    <div class="day-card-option" data-day-value="1">
                        <input type="radio" name="due_day" id="day-1" value="1">
                        <label for="day-1" class="day-card-label">
                            <span class="day-card-icon">📅</span>
                            <span class="day-card-text">الإثنين</span>
                        </label>
                    </div>
                    <div class="day-card-option" data-day-value="2">
                        <input type="radio" name="due_day" id="day-2" value="2">
                        <label for="day-2" class="day-card-label">
                            <span class="day-card-icon">📅</span>
                            <span class="day-card-text">الثلاثاء</span>
                        </label>
                    </div>
                    <div class="day-card-option" data-day-value="3">
                        <input type="radio" name="due_day" id="day-3" value="3">
                        <label for="day-3" class="day-card-label">
                            <span class="day-card-icon">📅</span>
                            <span class="day-card-text">الأربعاء</span>
                        </label>
                    </div>
                    <div class="day-card-option" data-day-value="4">
                        <input type="radio" name="due_day" id="day-4" value="4">
                        <label for="day-4" class="day-card-label">
                            <span class="day-card-icon">📅</span>
                            <span class="day-card-text">الخميس</span>
                        </label>
                    </div>
                    <div class="day-card-option" data-day-value="5">
                        <input type="radio" name="due_day" id="day-5" value="5">
                        <label for="day-5" class="day-card-label">
                            <span class="day-card-icon">📅</span>
                            <span class="day-card-text">الجمعة</span>
                        </label>
                    </div>
                    <div class="day-card-option" data-day-value="6">
                        <input type="radio" name="due_day" id="day-6" value="6">
                        <label for="day-6" class="day-card-label">
                            <span class="day-card-icon">📅</span>
                            <span class="day-card-text">السبت</span>
                        </label>
                    </div>
                </div>
            </div>
            <input type="hidden" id="taskDueDate" name="due_date">
            <div class="modal-footer">
                <button type="button" class="btn-cancel" id="cancelBtn">إلغاء</button>
                <button type="submit" class="btn-submit">حفظ</button>
            </div>
        </form>
    </div>
    </div>
</div>

@push('styles')
<style>
.tasks-container {
    padding: 30px;
    max-width: 100%;
    overflow-x: auto;
}

.tasks-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
    gap: 20px;
}

.header-left {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
}

.view-toggle {
    display: flex;
    gap: 8px;
    background: #f0f7fa;
    padding: 4px;
    border-radius: 10px;
    border: 2px solid #e0e6ed;
}

.view-toggle-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border: none;
    background: transparent;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: 'Cairo', sans-serif;
    font-size: 14px;
    font-weight: 500;
    color: #6c7a89;
}

.view-toggle-btn:hover {
    background: rgba(74, 144, 226, 0.1);
    color: #4a90e2;
}

.view-toggle-btn.active {
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(74, 144, 226, 0.3);
}

.view-toggle-btn .toggle-icon {
    font-size: 18px;
}

.view-toggle-btn .toggle-text {
    font-size: 14px;
}

@media (max-width: 768px) {
    .view-toggle-btn .toggle-text {
        display: none;
    }
    
    .view-toggle-btn {
        padding: 8px 12px;
    }
}

.tasks-title {
    font-size: 32px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
}

.current-month-display {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: linear-gradient(135deg, #e8f4f8 0%, #f0f7fa 100%);
    border-radius: 8px;
    border: 1px solid #4a90e2;
}

.month-label {
    font-size: 14px;
    color: #6c7a89;
    font-weight: 500;
}

.month-value {
    font-size: 16px;
    color: #4a90e2;
    font-weight: 600;
}

.month-selector {
    display: flex;
    align-items: center;
    gap: 10px;
}

.month-selector label {
    font-weight: 600;
    color: #2c3e50;
}

.month-input {
    padding: 10px 15px;
    border: 2px solid #e0e6ed;
    border-radius: 8px;
    font-size: 16px;
    font-family: 'Cairo', sans-serif;
    transition: all 0.3s ease;
}

.month-input:focus {
    outline: none;
    border-color: #4a90e2;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
}

.add-task-section {
    margin-bottom: 30px;
}

.add-task-section {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.btn-add-task,
.btn-add-bulk-tasks {
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    font-family: 'Cairo', sans-serif;
}

.btn-add-task:hover,
.btn-add-bulk-tasks:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3);
}

.btn-add-bulk-tasks {
    background: linear-gradient(135deg, #2d8659 0%, #1e5d3f 100%);
}

.btn-add-bulk-tasks:hover {
    box-shadow: 0 4px 12px rgba(45, 134, 89, 0.3);
}

.form-hint {
    display: block;
    margin-top: 6px;
    color: #6c7a89;
    font-size: 12px;
}

/* Tasks Rows Container */
.tasks-rows-container {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.status-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    transition: all 0.3s ease;
    cursor: move;
}

.status-section:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.status-section.dragging {
    opacity: 0.5;
    transform: scale(0.95);
}

.status-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e0e6ed;
    gap: 10px;
}

.drag-handle {
    font-size: 20px;
    color: #6c7a89;
    cursor: grab;
    user-select: none;
}

.drag-handle:active {
    cursor: grabbing;
}

.status-title {
    font-size: 20px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.employee-link {
    text-decoration: none;
    font-size: 18px;
    transition: transform 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: rgba(74, 144, 226, 0.1);
}

.employee-link:hover {
    transform: scale(1.2);
    background: rgba(74, 144, 226, 0.2);
}

.task-count {
    background: #4a90e2;
    color: white;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
}

.tasks-rows-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 15px;
}

.task-row {
    background: white;
    border-radius: 8px;
    padding: 20px;
    cursor: move;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border-left: 4px solid #4a90e2;
    width: 100%;
    max-width: 100%;
}

.task-row:hover {
    transform: translateX(-5px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.task-row.dragging {
    opacity: 0.5;
}

.task-row-content {
    width: 100%;
}

.task-row-main {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
}

.task-row-left {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    flex: 1;
}

.task-row-info {
    flex: 1;
}

.task-row-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.task-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 10px;
    font-size: 13px;
}

.task-priority {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}

.priority-1 { background: #95a5a6; }
.priority-2 { background: #3498db; }
.priority-3 { background: #f39c12; }
.priority-4 { background: #e67e22; }
.priority-5 { background: #e74c3c; }

.task-actions {
    display: flex;
    gap: 5px;
}

.btn-edit-task,
.btn-delete-task,
.btn-add-subtask {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 16px;
    padding: 5px;
    transition: transform 0.2s;
}

.btn-edit-task:hover,
.btn-delete-task:hover,
.btn-add-subtask:hover {
    transform: scale(1.2);
}

/* Subtasks Styles */
.subtasks-container {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 2px solid #e0e6ed;
}

.subtasks-header {
    margin-bottom: 10px;
}

.subtasks-toggle {
    cursor: pointer;
    color: #4a90e2;
    font-weight: 600;
    font-size: 14px;
    user-select: none;
    display: inline-block;
    padding: 5px 10px;
    border-radius: 5px;
    transition: background 0.2s;
}

.subtasks-toggle:hover {
    background: #f0f7ff;
}

.subtasks-list {
    margin-top: 10px;
    padding-right: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.subtask-row {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 12px;
    border-right: 3px solid #4a90e2;
    transition: all 0.3s ease;
    cursor: move;
}

.subtask-row:hover {
    background: #f0f7ff;
    transform: translateX(-3px);
    box-shadow: 0 2px 8px rgba(74, 144, 226, 0.15);
}

.subtask-content {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.subtask-priority {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 6px;
}

.subtask-info {
    flex: 1;
    min-width: 0;
}

.subtask-title-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 5px;
}

.subtask-title {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    color: #2c3e50;
}

.subtask-status-badge {
    font-size: 11px;
    padding: 3px 8px;
    border-radius: 12px;
    font-weight: 500;
}

.subtask-description {
    margin: 5px 0 0 0;
    font-size: 12px;
    color: #6c7a89;
    line-height: 1.4;
}

.subtask-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-top: 8px;
}

.subtask-tag {
    font-size: 10px;
    padding: 2px 6px;
    background: #e8f4f8;
    color: #2c3e50;
    border-radius: 10px;
}

.subtask-actions {
    display: flex;
    gap: 8px;
    align-items: center;
}

.btn-edit-subtask,
.btn-delete-subtask {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 14px;
    padding: 3px;
    transition: transform 0.2s;
}

.btn-edit-subtask:hover,
.btn-delete-subtask:hover {
    transform: scale(1.2);
}

.task-title-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
    flex-wrap: wrap;
}

.task-title {
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.task-status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}

.task-status-badge.status-todo {
    background: #e8f4f8;
    color: #4a90e2;
}

.task-status-badge.status-in_progress {
    background: #fff4e6;
    color: #f39c12;
}

.task-status-badge.status-done {
    background: #e8f5e9;
    color: #2d8659;
}

.task-description {
    font-size: 14px;
    color: #6c7a89;
    margin: 0 0 10px 0;
    line-height: 1.5;
}

.task-employee {
    color: #4a90e2;
    font-weight: 600;
}

.task-month {
    color: #667eea;
    font-weight: 600;
}

.task-project-container {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 10px 0;
    padding: 8px 12px;
    background: linear-gradient(135deg, #e8f5e9 0%, #f1f8f4 100%);
    border-radius: 8px;
    border: 2px solid #2d8659;
}

.task-project-label {
    color: #2d8659;
    font-weight: 700;
    font-size: 13px;
}

.task-project {
    color: #1e6b47;
    font-weight: 700;
    font-size: 14px;
}

.task-due-date {
    color: #6c7a89;
}

.task-due-date.overdue {
    color: #e74c3c;
    font-weight: 600;
}

/* Tags Styles */
.tags-input-container {
    position: relative;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 8px;
    background: white;
    min-height: 50px;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}

.tags-display {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    flex: 1;
}

.tag-item {
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 6px;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: scale(0.8);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.tag-item .tag-remove {
    cursor: pointer;
    font-size: 16px;
    line-height: 1;
    opacity: 0.8;
    transition: opacity 0.2s;
}

.tag-item .tag-remove:hover {
    opacity: 1;
}

.tags-input {
    flex: 1;
    min-width: 150px;
    border: none;
    outline: none;
    padding: 8px;
    font-size: 14px;
    font-family: 'Cairo', sans-serif;
    background: transparent;
}

.tags-hint {
    display: block;
    margin-top: 6px;
    color: #6c7a89;
    font-size: 12px;
}

.task-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin: 10px 0;
}

.task-tag {
    background: linear-gradient(135deg, #e8f4f8 0%, #f0f7fa 100%);
    color: #4a90e2;
    padding: 4px 12px;
    border-radius: 16px;
    font-size: 12px;
    font-weight: 500;
    border: 1px solid #4a90e2;
}

.task-types {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin: 10px 0;
}

.task-type-badge {
    background: linear-gradient(135deg, #fff4e6 0%, #ffe8cc 100%);
    color: #d97706;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
    border: 1px solid #fbbf24;
    display: inline-flex;
    align-items: center;
    gap: 4px;
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

.task-modal-content {
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

.modal-content form {
    padding: 30px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #2c3e50;
    font-size: 14px;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 15px;
    font-family: 'Cairo', sans-serif;
    transition: all 0.3s ease;
    background: white;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #4a90e2;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
}

/* Employee Cards */
.employee-cards-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 12px;
    margin-top: 10px;
}

.employee-card-option {
    position: relative;
}

.employee-card-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.employee-card-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 15px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    min-height: 80px;
}

.employee-card-label:hover {
    border-color: #4a90e2;
    background: #f0f7ff;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(74, 144, 226, 0.15);
}

.employee-card-option input[type="radio"]:checked + .employee-card-label {
    border-color: #4a90e2;
    background: linear-gradient(135deg, #e8f4f8 0%, #f0f7fa 100%);
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.2);
}

.employee-card-icon {
    font-size: 24px;
    margin-bottom: 8px;
}

.employee-card-text {
    font-size: 14px;
    font-weight: 500;
    color: #2c3e50;
    word-break: break-word;
}

/* Project Cards */
.project-cards-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 12px;
    margin-top: 10px;
}

.project-card-option {
    position: relative;
}

.project-card-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.project-card-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 15px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    min-height: 80px;
}

.project-card-label:hover {
    border-color: #4a90e2;
    background: #f0f7ff;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(74, 144, 226, 0.15);
}

.project-card-option input[type="radio"]:checked + .project-card-label {
    border-color: #4a90e2;
    background: linear-gradient(135deg, #e8f4f8 0%, #f0f7fa 100%);
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.2);
}

.project-card-icon {
    font-size: 24px;
    margin-bottom: 8px;
}

.project-card-text {
    font-size: 14px;
    font-weight: 500;
    color: #2c3e50;
    word-break: break-word;
}

/* Week Cards */
.week-cards-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 12px;
    margin-top: 10px;
}

.week-card-option {
    position: relative;
}

.week-card-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.week-card-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 15px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    min-height: 80px;
}

.week-card-label:hover {
    border-color: #4a90e2;
    background: #f0f7ff;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(74, 144, 226, 0.15);
}

.week-card-option input[type="radio"]:checked + .week-card-label {
    border-color: #4a90e2;
    background: linear-gradient(135deg, #e8f4f8 0%, #f0f7fa 100%);
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.2);
}

.week-card-icon {
    font-size: 24px;
    margin-bottom: 8px;
}

.week-card-text {
    font-size: 14px;
    font-weight: 500;
    color: #2c3e50;
    word-break: break-word;
}

/* Task Types Cards */
.task-types-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 12px;
    margin-top: 10px;
}

.task-type-card {
    position: relative;
}

.task-type-card input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.task-type-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 15px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    min-height: 80px;
}

.task-type-label:hover {
    border-color: #4a90e2;
    background: #f0f7ff;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(74, 144, 226, 0.15);
}

.task-type-card input[type="checkbox"]:checked + .task-type-label {
    border-color: #4a90e2;
    background: linear-gradient(135deg, #e8f4f8 0%, #f0f7fa 100%);
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.2);
}

.task-type-icon {
    font-size: 24px;
    margin-bottom: 8px;
}

.task-type-text {
    font-size: 14px;
    font-weight: 500;
    color: #2c3e50;
    word-break: break-word;
}

/* Day Cards */
.day-cards-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 12px;
    margin-top: 10px;
}

.day-card-option {
    position: relative;
}

.day-card-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.day-card-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 12px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    min-height: 70px;
}

.day-card-label:hover {
    border-color: #4a90e2;
    background: #f0f7ff;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(74, 144, 226, 0.15);
}

.day-card-option input[type="radio"]:checked + .day-card-label {
    border-color: #4a90e2;
    background: linear-gradient(135deg, #e8f4f8 0%, #f0f7fa 100%);
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.2);
}

.day-card-icon {
    font-size: 20px;
    margin-bottom: 6px;
}

.day-card-text {
    font-size: 13px;
    font-weight: 500;
    color: #2c3e50;
    word-break: break-word;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #e8f4f8;
}

.btn-cancel,
.btn-submit {
    flex: 1;
    padding: 14px;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: 'Cairo', sans-serif;
}

.btn-cancel {
    background: #e8f4f8;
    color: #5a6c7d;
}

.btn-cancel:hover {
    background: #d0e8f2;
}

.btn-submit {
    background: linear-gradient(135deg, #5ba3d4 0%, #4a90e2 100%);
    color: white;
}

.btn-submit:hover {
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3);
}

/* Responsive */
@media (max-width: 1024px) {
    .tasks-rows-list {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 12px;
    }
    
    .task-row-main {
        flex-direction: column;
        gap: 15px;
    }
    
    .task-row-actions {
        align-self: flex-end;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .task-modal-content {
        width: 95%;
        margin: 10% auto;
    }
    
    .modal-content form {
        padding: 20px;
    }
    
    .employee-cards-container,
    .project-cards-container,
    .week-cards-container,
    .day-cards-container {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 10px;
    }
    
    .calendar-container {
        grid-template-columns: 1fr;
    }
    
    .calendar-day {
        min-height: 60px;
    }
}

@media (max-width: 768px) {
    .tasks-rows-list {
        grid-template-columns: 1fr;
    }
}

/* Calendar Styles */
.calendar-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
    margin-top: 20px;
}

.calendar-wrapper {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e0e6ed;
}

.calendar-nav-btn {
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
    color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 8px;
    font-size: 24px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.calendar-nav-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3);
}

.calendar-month-year {
    font-size: 24px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
}

.calendar-day-header {
    text-align: center;
    padding: 10px;
    font-weight: 600;
    color: #4a90e2;
    font-size: 14px;
    background: #f0f7fa;
    border-radius: 8px;
}

.calendar-day {
    aspect-ratio: 1;
    border: 2px solid #e0e6ed;
    border-radius: 8px;
    padding: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    min-height: 80px;
}

.calendar-day:hover {
    border-color: #4a90e2;
    background: #f0f7ff;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(74, 144, 226, 0.15);
}

.calendar-day.other-month {
    opacity: 0.3;
    background: #f8f9fa;
}

.calendar-day.today {
    border-color: #4a90e2;
    background: linear-gradient(135deg, #e8f4f8 0%, #f0f7fa 100%);
    font-weight: 600;
}

.calendar-day.selected {
    border-color: #4a90e2;
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
    color: white;
}

.calendar-day-number {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 4px;
}

.calendar-day-tasks {
    display: flex;
    flex-direction: column;
    gap: 2px;
    width: 100%;
    flex: 1;
    overflow: hidden;
}

.calendar-task-dot {
    width: 100%;
    height: 4px;
    border-radius: 2px;
    font-size: 8px;
    display: flex;
    align-items: center;
    padding: 0 2px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.calendar-task-dot.priority-1 { background: #95a5a6; }
.calendar-task-dot.priority-2 { background: #3498db; }
.calendar-task-dot.priority-3 { background: #f39c12; }
.calendar-task-dot.priority-4 { background: #e67e22; }
.calendar-task-dot.priority-5 { background: #e74c3c; }

.calendar-task-count {
    font-size: 10px;
    color: #6c7a89;
    margin-top: 4px;
}

.calendar-day.selected .calendar-task-count {
    color: rgba(255, 255, 255, 0.9);
}

.calendar-tasks-panel {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    max-height: 600px;
    overflow-y: auto;
}

.calendar-tasks-panel h3 {
    margin: 0 0 20px 0;
    font-size: 20px;
    font-weight: 600;
    color: #2c3e50;
    padding-bottom: 15px;
    border-bottom: 2px solid #e0e6ed;
}

.selected-date-tasks {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.no-tasks-message {
    text-align: center;
    color: #6c7a89;
    padding: 40px 20px;
    font-size: 16px;
}

.calendar-task-item {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 12px;
    border-left: 4px solid #4a90e2;
    transition: all 0.3s ease;
}

.calendar-task-item:hover {
    background: #f0f7ff;
    transform: translateX(-3px);
    box-shadow: 0 2px 8px rgba(74, 144, 226, 0.15);
}

.calendar-task-item-title {
    font-size: 14px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 6px;
}

.calendar-task-item-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    font-size: 12px;
    color: #6c7a89;
}

.calendar-task-item-meta span {
    display: flex;
    align-items: center;
    gap: 4px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Month Selection Screen
    const monthSelectionScreen = document.getElementById('monthSelectionScreen');
    const tasksMainContent = document.getElementById('tasksMainContent');
    const monthSelectMain = document.getElementById('monthSelectMain');
    const loadTasksBtn = document.getElementById('loadTasksBtn');
    
    // Load tasks when button is clicked
    if (loadTasksBtn && monthSelectMain) {
        loadTasksBtn.addEventListener('click', function() {
            const selectedMonth = monthSelectMain.value;
            if (selectedMonth) {
                window.location.href = `{{ route('tasks.index') }}?month=${selectedMonth}`;
            }
        });
        
        // Also allow Enter key
        monthSelectMain.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                loadTasksBtn.click();
            }
        });
    }
    
    const monthSelect = document.getElementById('monthSelect');
    const addTaskBtn = document.getElementById('addTaskBtn');
    const taskModal = document.getElementById('taskModal');
    const closeModal = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const taskForm = document.getElementById('taskForm');
    const modalTitle = document.getElementById('modalTitle');
    const taskIdInput = document.getElementById('taskId');
    const viewByEmployeeBtn = document.getElementById('viewByEmployee');
    const viewByProjectBtn = document.getElementById('viewByProject');
    const tasksContainerByEmployee = document.getElementById('tasksRowsContainer');
    const tasksContainerByProject = document.getElementById('tasksRowsContainerByProject');
    
    let draggedTask = null;
    let draggedOverColumn = null;
    let draggedSection = null;
    
    // استعادة طريقة العرض المحفوظة
    let currentView = localStorage.getItem('tasksView') || 'employee'; // 'employee' or 'project'
    
    // تطبيق طريقة العرض المحفوظة عند التحميل
    if (tasksContainerByEmployee && tasksContainerByProject && viewByEmployeeBtn && viewByProjectBtn) {
        if (currentView === 'project') {
            viewByProjectBtn.classList.add('active');
            viewByEmployeeBtn.classList.remove('active');
            tasksContainerByEmployee.style.display = 'none';
            tasksContainerByProject.style.display = 'flex';
        } else {
            viewByEmployeeBtn.classList.add('active');
            viewByProjectBtn.classList.remove('active');
            tasksContainerByEmployee.style.display = 'flex';
            tasksContainerByProject.style.display = 'none';
        }
    }

    // التبديل بين طريقة العرض
    viewByEmployeeBtn.addEventListener('click', function() {
        currentView = 'employee';
        localStorage.setItem('tasksView', 'employee'); // حفظ طريقة العرض
        viewByEmployeeBtn.classList.add('active');
        viewByProjectBtn.classList.remove('active');
        tasksContainerByEmployee.style.display = 'flex';
        tasksContainerByProject.style.display = 'none';
    });

    viewByProjectBtn.addEventListener('click', function() {
        currentView = 'project';
        localStorage.setItem('tasksView', 'project'); // حفظ طريقة العرض
        viewByProjectBtn.classList.add('active');
        viewByEmployeeBtn.classList.remove('active');
        tasksContainerByEmployee.style.display = 'none';
        tasksContainerByProject.style.display = 'flex';
    });

    // إدارة أنواع المهام
    const taskTypeCards = document.querySelectorAll('.task-type-card');
    const taskTypesHidden = document.getElementById('taskTypesHidden');
    
    function updateTaskTypesHidden() {
        if (!taskTypesHidden) return;
        const selectedTypes = Array.from(document.querySelectorAll('input[name="task_types[]"]:checked'))
            .map(checkbox => checkbox.value);
        taskTypesHidden.value = JSON.stringify(selectedTypes);
    }
    
    if (taskTypeCards.length > 0) {
        taskTypeCards.forEach(card => {
            const checkbox = card.querySelector('input[type="checkbox"]');
            const label = card.querySelector('.task-type-label');
            
            if (label) {
                // النقر على الكارد يغير حالة checkbox
                label.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (checkbox) {
                        checkbox.checked = !checkbox.checked;
                        updateTaskTypesHidden();
                    }
                });
            }
            
            // تحديث عند تغيير checkbox مباشرة
            if (checkbox) {
                checkbox.addEventListener('change', function() {
                    updateTaskTypesHidden();
                });
            }
        });
    }

    // تغيير الشهر
    monthSelect.addEventListener('change', function() {
        const month = this.value;
        window.location.href = `{{ route('tasks.index') }}?month=${month}`;
    });


    // فتح Modal لإضافة مهمة
    addTaskBtn.addEventListener('click', function() {
        modalTitle.textContent = 'إضافة مهمة جديدة';
        taskForm.reset();
        taskIdInput.value = '';
        document.getElementById('taskMonth').value = '{{ $selectedMonth }}';
        // إعادة تعيين أنواع المهام
        document.querySelectorAll('input[name="task_types[]"]').forEach(checkbox => {
            checkbox.checked = false;
        });
        updateTaskTypesHidden();
        // إظهار حقول الموظف والمشروع
        const employeeFormGroup = document.getElementById('employeeFormGroup');
        const projectFormGroup = document.getElementById('projectFormGroup');
        if (employeeFormGroup) employeeFormGroup.style.display = '';
        if (projectFormGroup) projectFormGroup.style.display = '';
        // إخفاء حقول الشهر والأسبوع عند إضافة مهمة جديدة
        const monthFormGroup = document.getElementById('monthFormGroup');
        const weekFormGroup = document.getElementById('weekFormGroup');
        if (monthFormGroup) monthFormGroup.style.display = 'none';
        if (weekFormGroup) weekFormGroup.style.display = 'none';
        // إلغاء تحديد جميع الأسابيع
        document.querySelectorAll('input[name="due_week"]').forEach(radio => {
            radio.checked = false;
        });
        document.getElementById('week-none').checked = true;
        // إخفاء اختيار الأيام
        document.getElementById('daySelectionGroup').style.display = 'none';
        // إلغاء تحديد جميع الأيام
        document.querySelectorAll('input[name="due_day"]').forEach(radio => {
            radio.checked = false;
        });
        document.getElementById('taskDueDate').value = '';
        taskModal.style.display = 'block';
    });
    
    // دالة لحساب التاريخ من الأسبوع واليوم
    function calculateDueDate() {
        const month = document.getElementById('taskMonth').value;
        const weekRadio = document.querySelector('input[name="due_week"]:checked');
        const dayRadio = document.querySelector('input[name="due_day"]:checked');
        
        if (!month || !weekRadio || !dayRadio || weekRadio.value === '' || dayRadio.value === '') {
            document.getElementById('taskDueDate').value = '';
            return;
        }
        
        const week = weekRadio.value;
        const day = dayRadio.value;
        
        const [year, monthNum] = month.split('-');
        const targetDay = parseInt(day); // 0 = الأحد, 1 = الإثنين, ...
        const weekNum = parseInt(week); // 1, 2, 3, 4
        
        // حساب أول يوم في الأسبوع المحدد
        // الأسبوع الأول: الأيام 1-7
        // الأسبوع الثاني: الأيام 8-14
        // الأسبوع الثالث: الأيام 15-21
        // الأسبوع الرابع: الأيام 22-28
        const weekStartDay = 1 + (weekNum - 1) * 7;
        
        // حساب التاريخ المطلوب
        // نبدأ من أول يوم في الأسبوع المحدد
        let date = new Date(year, monthNum - 1, weekStartDay);
        
        // حساب الفرق بين يوم الأسبوع المطلوب وأول يوم في الأسبوع
        const dayDiff = (targetDay - date.getDay() + 7) % 7;
        date.setDate(date.getDate() + dayDiff);
        
        // التأكد من أن التاريخ في نفس الشهر
        const lastDayOfMonth = new Date(year, monthNum, 0).getDate();
        if (date.getDate() > lastDayOfMonth) {
            // إذا تجاوز الشهر، نستخدم آخر يوم في الشهر
            date = new Date(year, monthNum - 1, lastDayOfMonth);
        }
        
        // تحويل إلى صيغة YYYY-MM-DD
        const yearStr = date.getFullYear();
        const monthStr = String(date.getMonth() + 1).padStart(2, '0');
        const dayStr = String(date.getDate()).padStart(2, '0');
        document.getElementById('taskDueDate').value = `${yearStr}-${monthStr}-${dayStr}`;
    }
    
    // تفعيل اختيار الأيام عند اختيار الأسبوع
    document.querySelectorAll('input[name="due_week"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const daySelectionGroup = document.getElementById('daySelectionGroup');
            if (this.value && this.value !== '') {
                daySelectionGroup.style.display = 'block';
                // إلغاء تحديد جميع الأيام
                document.querySelectorAll('input[name="due_day"]').forEach(dayRadio => {
                    dayRadio.checked = false;
                });
                document.getElementById('taskDueDate').value = '';
            } else {
                daySelectionGroup.style.display = 'none';
                // إلغاء تحديد جميع الأيام
                document.querySelectorAll('input[name="due_day"]').forEach(dayRadio => {
                    dayRadio.checked = false;
                });
                document.getElementById('taskDueDate').value = '';
            }
        });
    });
    
    // حساب التاريخ عند اختيار اليوم
    document.querySelectorAll('input[name="due_day"]').forEach(radio => {
        radio.addEventListener('change', function() {
            calculateDueDate();
        });
    });
    
    // إعادة حساب التاريخ عند تغيير الشهر
    document.getElementById('taskMonth').addEventListener('change', function() {
        const weekRadio = document.querySelector('input[name="due_week"]:checked');
        const dayRadio = document.querySelector('input[name="due_day"]:checked');
        if (weekRadio && dayRadio && weekRadio.value && dayRadio.value) {
            calculateDueDate();
        }
    });

    // إغلاق Modal
    if (closeModal) {
        closeModal.addEventListener('click', function() {
            taskModal.style.display = 'none';
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            taskModal.style.display = 'none';
        });
    }

    // إعادة تعيين عند إغلاق المودال
    if (taskModal) {
        taskModal.addEventListener('click', function(e) {
            if (e.target === taskModal) {
                // إعادة تعيين parent_id
                const parentIdInput = document.getElementById('parentId');
                if (parentIdInput) {
                    parentIdInput.value = '';
                }
                // إظهار حقول الموظف والمشروع والشهر بالكامل
                const employeeFormGroup = document.getElementById('employeeFormGroup');
                const projectFormGroup = document.getElementById('projectFormGroup');
                const monthFormGroup = document.getElementById('monthFormGroup');
                if (employeeFormGroup) employeeFormGroup.style.display = '';
                if (projectFormGroup) projectFormGroup.style.display = '';
                if (monthFormGroup) monthFormGroup.style.display = '';
                // تحديد "بدون موظف" كافتراضي
                const employeeNone = document.getElementById('employee-none');
                if (employeeNone) {
                    employeeNone.checked = true;
                }
                // تحديد "بدون مشروع" كافتراضي
                const projectNone = document.getElementById('project-none');
                if (projectNone) {
                    projectNone.checked = true;
                }
            }
        });
    }

    // إغلاق Modal عند النقر خارجها
    window.addEventListener('click', function(e) {
        if (e.target === taskModal) {
            taskModal.style.display = 'none';
        }
    });

    // Drag and Drop للمهام
    const taskCards = document.querySelectorAll('.task-row');
    const taskLists = document.querySelectorAll('.tasks-rows-list');
    
    // Drag and Drop لأقسام الموظفين
    const employeeSections = document.querySelectorAll('.status-section[data-section-type="employee"]');
    const tasksContainer = document.getElementById('tasksRowsContainer');

    taskCards.forEach(card => {
        card.addEventListener('dragstart', function(e) {
            draggedTask = this;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        });

        card.addEventListener('dragend', function() {
            this.classList.remove('dragging');
            // إزالة جميع placeholders المتبقية
            document.querySelectorAll('.task-placeholder').forEach(placeholder => {
                placeholder.remove();
            });
            draggedTask = null;
        });
    });

    taskLists.forEach(list => {
        let placeholder = null;
        
        list.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            draggedOverColumn = this;
            
            // إظهار موضع الإدراج
            if (draggedTask) {
                const afterElement = getDragAfterTaskElement(this, e.clientY);
                
                // إزالة placeholder السابق إن وجد
                if (placeholder) {
                    placeholder.remove();
                }
                
                // إنشاء placeholder جديد
                placeholder = document.createElement('div');
                placeholder.classList.add('task-placeholder');
                placeholder.style.height = draggedTask.offsetHeight + 'px';
                placeholder.style.border = '2px dashed #007bff';
                placeholder.style.borderRadius = '8px';
                placeholder.style.margin = '8px 0';
                
                if (afterElement == null) {
                    // إضافة في النهاية
                    this.appendChild(placeholder);
                } else {
                    // إدراج قبل العنصر المحدد
                    this.insertBefore(placeholder, afterElement);
                }
            }
        });
        
        list.addEventListener('dragleave', function(e) {
            // إزالة placeholder عند مغادرة المنطقة
            if (placeholder && !this.contains(e.relatedTarget)) {
                placeholder.remove();
                placeholder = null;
            }
        });

        list.addEventListener('drop', function(e) {
            e.preventDefault();
            if (draggedTask) {
                // إزالة placeholder إن وجد
                if (placeholder) {
                    placeholder.remove();
                    placeholder = null;
                }
                
                // التأكد من أن المهمة في المكان الصحيح
                if (!this.contains(draggedTask)) {
                    this.appendChild(draggedTask);
                }
                
                const newEmployeeId = this.dataset.employeeId;
                
                // تحديث ترتيب جميع المهام في القسم مباشرة
                updateAllTasksOrderInSection(this, newEmployeeId);
            }
        });
    });
    
    // دالة للحصول على المهمة التي يجب إدراج المهمة بعدها
    function getDragAfterTaskElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.task-row:not(.dragging)')];
        
        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    // Drag and Drop لأقسام الموظفين
    employeeSections.forEach(section => {
        section.addEventListener('dragstart', function(e) {
            draggedSection = this;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.outerHTML);
        });

        section.addEventListener('dragend', function() {
            this.classList.remove('dragging');
            draggedSection = null;
        });
    });

    // السماح بإفلات أقسام الموظفين
    tasksContainer.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        
        if (draggedSection) {
            const afterElement = getDragAfterElement(tasksContainer, e.clientY);
            if (afterElement == null) {
                tasksContainer.appendChild(draggedSection);
            } else {
                tasksContainer.insertBefore(draggedSection, afterElement);
            }
        }
    });

    tasksContainer.addEventListener('drop', function(e) {
        e.preventDefault();
        if (draggedSection) {
            updateEmployeeOrder();
        }
    });

    // دالة للحصول على العنصر الذي يجب إدراج العنصر بعده
    function getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.status-section:not(.dragging)')];
        
        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    // تحديث ترتيب الموظفين
    function updateEmployeeOrder() {
        const sections = Array.from(tasksContainer.querySelectorAll('.status-section[data-section-type="employee"]'));
        const employees = sections.map((section, index) => ({
            id: section.dataset.employeeId,
            order: index + 1
        }));

        fetch('{{ route("employees.update-order") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ employees: employees })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('تم تحديث ترتيب الموظفين بنجاح');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    // تحديث ترتيب جميع المهام في قسم معين
    function updateAllTasksOrderInSection(taskList, newEmployeeId) {
        const tasks = Array.from(taskList.querySelectorAll('.task-row'));
        const tasksData = [];
        
        tasks.forEach((task, index) => {
            const statusBadge = task.querySelector('.task-status-badge');
            let status = 'todo';
            if (statusBadge) {
                if (statusBadge.classList.contains('status-done')) {
                    status = 'done';
                } else if (statusBadge.classList.contains('status-in_progress')) {
                    status = 'in_progress';
                }
            }
            
            tasksData.push({
                id: task.dataset.taskId,
                status: status,
                order: index + 1,
                employee_id: newEmployeeId || null
            });
        });
        
        if (tasksData.length === 0) {
            console.log('لا توجد مهام لتحديثها');
            return;
        }
        
        console.log('تحديث ترتيب المهام:', tasksData);
        
        // تحديث جميع المهام دفعة واحدة
        Promise.all(tasksData.map(taskData => {
            const updateData = {
                order: taskData.order,
                status: taskData.status
            };
            
            // إضافة employee_id فقط إذا تغير
            if (taskData.employee_id !== undefined) {
                updateData.employee_id = taskData.employee_id;
            }
            
            return fetch(`{{ route('tasks.update', ':id') }}`.replace(':id', taskData.id), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(updateData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log(`تم تحديث المهمة ${taskData.id}:`, data);
                return data;
            })
            .catch(error => {
                console.error(`خطأ في تحديث المهمة ${taskData.id}:`, error);
                return { success: false, error: error.message };
            });
        }))
        .then(results => {
            const allSuccess = results.every(result => result && result.success);
            if (allSuccess) {
                console.log('✅ تم تحديث ترتيب جميع المهام بنجاح');
                updateTaskCounts();
            } else {
                console.error('❌ حدث خطأ في تحديث بعض المهام:', results);
            }
        })
        .catch(error => {
            console.error('❌ Error:', error);
        });
    }

    // تحديث عدد المهام
    function updateTaskCounts() {
        // تحديث عدد المهام بدون موظف
        const noEmployeeCount = document.querySelectorAll('#tasks-no-employee .task-row').length;
        const noEmployeeCountElement = document.getElementById('count-no-employee');
        if (noEmployeeCountElement) {
            noEmployeeCountElement.textContent = noEmployeeCount;
        }
        
        @foreach($employees as $employee)
        const count{{ $employee->id }} = document.querySelectorAll(`#tasks-{{ $employee->id }} .task-row`).length;
        const countElement{{ $employee->id }} = document.getElementById('count-{{ $employee->id }}');
        if (countElement{{ $employee->id }}) {
            countElement{{ $employee->id }}.textContent = count{{ $employee->id }};
        }
        @endforeach
    }

    // تحديث العدد عند التحميل
    updateTaskCounts();

    // تعديل المهمة
    document.querySelectorAll('.btn-edit-task').forEach(btn => {
        btn.addEventListener('click', function() {
            const taskId = this.dataset.taskId;
            fetch(`{{ url('/tasks') }}/${taskId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.task) {
                        const parentIdInput = document.getElementById('parentId');
                        if (parentIdInput) {
                            parentIdInput.value = data.task.parent_id || '';
                        }
                        // إظهار/إخفاء حقول الموظف والمشروع والشهر والأسبوع حسب نوع المهمة
                        const employeeFormGroup = document.getElementById('employeeFormGroup');
                        const projectFormGroup = document.getElementById('projectFormGroup');
                        const monthFormGroup = document.getElementById('monthFormGroup');
                        const weekFormGroup = document.getElementById('weekFormGroup');
                        // إخفاء الشهر والأسبوع دائماً عند التعديل
                        if (monthFormGroup) monthFormGroup.style.display = 'none';
                        if (weekFormGroup) weekFormGroup.style.display = 'none';
                        if (data.task.parent_id) {
                            // مهمة فرعية - إخفاء الحقول
                            if (employeeFormGroup) employeeFormGroup.style.display = 'none';
                            if (projectFormGroup) projectFormGroup.style.display = 'none';
                            modalTitle.textContent = 'تعديل مهمة فرعية';
                        } else {
                            // مهمة رئيسية - إظهار الحقول
                            if (employeeFormGroup) employeeFormGroup.style.display = '';
                            if (projectFormGroup) projectFormGroup.style.display = '';
                            modalTitle.textContent = 'تعديل المهمة';
                        }
                        taskIdInput.value = data.task.id;
                        // تحديد الموظف المحدد
                        const employeeId = data.task.employee_id || '';
                        const employeeRadio = document.querySelector(`input[name="employee_id"][value="${employeeId}"]`);
                        if (employeeRadio) {
                            employeeRadio.checked = true;
                        } else {
                            // إذا لم يوجد موظف، تحديد "بدون موظف"
                            const employeeNone = document.getElementById('employee-none');
                            if (employeeNone) {
                                employeeNone.checked = true;
                            }
                        }
                        // تحديد المشروع المحدد
                        const projectId = data.task.project_id || '';
                        const projectRadio = document.querySelector(`input[name="project_id"][value="${projectId}"]`);
                        if (projectRadio) {
                            projectRadio.checked = true;
                        }
                        document.getElementById('taskTitle').value = data.task.title;
                        document.getElementById('taskDescription').value = data.task.description || '';
                        document.getElementById('taskMonth').value = data.task.month || '{{ $selectedMonth }}';
                        
                        // تحميل أنواع المهام
                        const taskTypes = data.task.task_types || [];
                        document.querySelectorAll('input[name="task_types[]"]').forEach(checkbox => {
                            checkbox.checked = taskTypes.includes(checkbox.value);
                        });
                        if (typeof updateTaskTypesHidden === 'function') {
                            updateTaskTypesHidden();
                        }
                        
                        // تحميل تاريخ الاستحقاق فقط (بدون الأسبوع واليوم)
                        if (data.task.due_date) {
                            document.getElementById('taskDueDate').value = data.task.due_date;
                        } else {
                            document.getElementById('taskDueDate').value = '';
                        }
                        
                        taskModal.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    });

    // ========== Subtasks Functions ==========
    // إضافة مهمة فرعية
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-add-subtask')) {
            const parentTaskId = e.target.closest('.btn-add-subtask').getAttribute('data-task-id');
            const parentIdInput = document.getElementById('parentId');
            if (parentIdInput) {
                parentIdInput.value = parentTaskId;
            }
            // إعادة تعيين النموذج
            taskForm.reset();
            taskIdInput.value = '';
            modalTitle.textContent = 'إضافة مهمة فرعية';
            document.getElementById('taskMonth').value = '{{ $selectedMonth }}';
            // إخفاء حقول الموظف والمشروع والشهر بالكامل لأنها ستُنسخ من المهمة الرئيسية
            const employeeFormGroup = document.getElementById('employeeFormGroup');
            const projectFormGroup = document.getElementById('projectFormGroup');
            const monthFormGroup = document.getElementById('monthFormGroup');
            if (employeeFormGroup) employeeFormGroup.style.display = 'none';
            if (projectFormGroup) projectFormGroup.style.display = 'none';
            if (monthFormGroup) monthFormGroup.style.display = 'none';
            taskModal.style.display = 'block';
        }
        
        // تعديل مهمة فرعية
        if (e.target.closest('.btn-edit-subtask')) {
            const subtaskId = e.target.closest('.btn-edit-subtask').getAttribute('data-task-id');
            fetch(`{{ url('/tasks') }}/${subtaskId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.task) {
                        const task = data.task;
                        taskIdInput.value = task.id;
                        const parentIdInput = document.getElementById('parentId');
                        if (parentIdInput) {
                            parentIdInput.value = task.parent_id || '';
                        }
                        document.getElementById('taskTitle').value = task.title || '';
                        document.getElementById('taskDescription').value = task.description || '';
                        document.getElementById('taskMonth').value = task.month || '';
                        
                        // تحميل أنواع المهام
                        const taskTypes = task.task_types || [];
                        document.querySelectorAll('input[name="task_types[]"]').forEach(checkbox => {
                            checkbox.checked = taskTypes.includes(checkbox.value);
                        });
                        if (typeof updateTaskTypesHidden === 'function') {
                            updateTaskTypesHidden();
                        }
                        
                        modalTitle.textContent = 'تعديل مهمة فرعية';
                        // إخفاء حقول الموظف والمشروع والشهر بالكامل
                        const employeeFormGroup = document.getElementById('employeeFormGroup');
                        const projectFormGroup = document.getElementById('projectFormGroup');
                        const monthFormGroup = document.getElementById('monthFormGroup');
                        if (employeeFormGroup) employeeFormGroup.style.display = 'none';
                        if (projectFormGroup) projectFormGroup.style.display = 'none';
                        if (monthFormGroup) monthFormGroup.style.display = 'none';
                        taskModal.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('حدث خطأ أثناء جلب بيانات المهمة');
                });
        }
        
        // حذف مهمة فرعية
        if (e.target.closest('.btn-delete-subtask')) {
            const subtaskId = e.target.closest('.btn-delete-subtask').getAttribute('data-task-id');
            if (confirm('هل أنت متأكد من حذف هذه المهمة الفرعية؟')) {
                fetch(`{{ url('/tasks') }}/${subtaskId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'حدث خطأ أثناء حذف المهمة');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('حدث خطأ أثناء حذف المهمة');
                });
            }
        }
        
        // تبديل عرض/إخفاء المهام الفرعية
        if (e.target.closest('.subtasks-toggle')) {
            const parentTaskId = e.target.closest('.subtasks-toggle').getAttribute('data-parent-task-id');
            const subtasksList = document.getElementById(`subtasks-${parentTaskId}`);
            const toggle = e.target.closest('.subtasks-toggle');
            if (subtasksList && toggle) {
                const isHidden = subtasksList.style.display === 'none';
                subtasksList.style.display = isHidden ? 'block' : 'none';
                toggle.textContent = isHidden ? `▲ إخفاء المهام الفرعية` : `▼ المهام الفرعية`;
            }
        }
    });

    // حذف المهمة
    document.querySelectorAll('.btn-delete-task').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('هل أنت متأكد من حذف هذه المهمة؟')) {
                const taskId = this.dataset.taskId;
                fetch(`{{ url('/tasks') }}/${taskId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector(`[data-task-id="${taskId}"]`).remove();
                        updateTaskCounts();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    });

    // حفظ المهمة
    taskForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const taskId = taskIdInput.value;
        const url = taskId 
            ? `{{ url('/tasks') }}/${taskId}`
            : '{{ route('tasks.store') }}';
        const method = taskId ? 'PUT' : 'POST';

        const weekRadio = document.querySelector('input[name="due_week"]:checked');
        const dayRadio = document.querySelector('input[name="due_day"]:checked');
        
        // إعادة حساب التاريخ قبل الإرسال
        if (weekRadio && dayRadio && weekRadio.value && dayRadio.value) {
            calculateDueDate();
        }
        
        const data = {
            parent_id: formData.get('parent_id') || null,
            employee_id: formData.get('employee_id') || null,
            project_id: formData.get('project_id') || null,
            title: formData.get('title'),
            description: formData.get('description'),
            status: 'todo', // قيمة افتراضية
            priority: 1, // قيمة افتراضية
            due_date: document.getElementById('taskDueDate').value || null,
            month: formData.get('month'),
            task_types: document.getElementById('taskTypesHidden') ? JSON.parse(document.getElementById('taskTypesHidden').value || '[]') : []
        };

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                taskModal.style.display = 'none';
                location.reload();
            } else {
                alert(data.message || 'حدث خطأ');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء حفظ المهمة');
        });
    });
});
</script>
@endpush
@endsection

