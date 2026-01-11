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
                    @if($task->tags && count($task->tags) > 0)
                    <div class="task-tags">
                        @foreach($task->tags as $tag)
                        <span class="task-tag">🏷️ {{ $tag }}</span>
                        @endforeach
                    </div>
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
                        @if($task->due_date)
                        @php
                            $dueDate = \Carbon\Carbon::parse($task->due_date);
                            $dayOfMonth = $dueDate->day;
                            $weekNum = min(max(ceil($dayOfMonth / 7), 1), 4);
                            $dayOfWeek = $dueDate->dayOfWeek; // 0 = الأحد, 6 = السبت
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
                <button class="btn-add-subtask" data-task-id="{{ $task->id }}" title="إضافة مهمة فرعية">
                    ➕
                </button>
                <button class="btn-edit-task" data-task-id="{{ $task->id }}" title="تعديل">
                    ✏️
                </button>
                <button class="btn-delete-task" data-task-id="{{ $task->id }}" title="حذف">
                    🗑️
                </button>
            </div>
        </div>
        @if($task->subtasks && $task->subtasks->count() > 0)
        <div class="subtasks-container" data-parent-task-id="{{ $task->id }}">
            <div class="subtasks-header">
                <span class="subtasks-toggle" data-parent-task-id="{{ $task->id }}">▼ المهام الفرعية ({{ $task->subtasks->count() }})</span>
            </div>
            <div class="subtasks-list" id="subtasks-{{ $task->id }}" style="display: none;">
                @foreach($task->subtasks as $subtask)
                <div class="subtask-row" data-task-id="{{ $subtask->id }}" draggable="true">
                    <div class="subtask-content">
                        <span class="subtask-priority priority-{{ $subtask->priority }}"></span>
                        <div class="subtask-info">
                            <div class="subtask-title-row">
                                <h5 class="subtask-title">{{ $subtask->title }}</h5>
                                <span class="subtask-status-badge status-{{ $subtask->status }}">
                                    @if($subtask->status == 'todo') قيد الانتظار
                                    @elseif($subtask->status == 'in_progress') قيد التنفيذ
                                    @elseif($subtask->status == 'done') مكتملة
                                    @endif
                                </span>
                            </div>
                            @if($subtask->description)
                            <p class="subtask-description">{{ $subtask->description }}</p>
                            @endif
                            @if($subtask->tags && count($subtask->tags) > 0)
                            <div class="subtask-tags">
                                @foreach($subtask->tags as $tag)
                                <span class="subtask-tag">🏷️ {{ $tag }}</span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        <div class="subtask-actions">
                            <button class="btn-edit-subtask" data-task-id="{{ $subtask->id }}" title="تعديل">
                                ✏️
                            </button>
                            <button class="btn-delete-subtask" data-task-id="{{ $subtask->id }}" title="حذف">
                                🗑️
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>


