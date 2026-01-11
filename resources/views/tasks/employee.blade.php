<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>مهام {{ $employee->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            direction: rtl;
        }

        .employee-tasks-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 30px;
        }

        .employee-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }

        .employee-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .employee-header .month-display {
            color: #666;
            font-size: 16px;
        }

        .tasks-list {
            display: grid;
            gap: 20px;
        }

        .task-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .task-card:hover {
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .task-title {
            font-size: 20px;
            color: #333;
            font-weight: bold;
            flex: 1;
        }

        .task-project {
            background: #667eea;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            margin-top: 5px;
        }

        .task-description {
            color: #666;
            margin: 10px 0;
            line-height: 1.6;
        }

        .task-types {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 10px 0;
        }

        .task-type-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .task-due-date {
            color: #666;
            font-size: 14px;
            margin-top: 10px;
        }

        .subtasks-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .subtasks-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .subtasks-header h3 {
            color: #333;
            font-size: 18px;
        }

        .btn-add-subtask {
            background: #667eea;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .btn-add-subtask:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }

        .subtasks-list {
            display: grid;
            gap: 10px;
        }

        .subtask-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .subtask-info {
            flex: 1;
        }

        .subtask-title {
            font-size: 16px;
            color: #333;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .subtask-actions {
            display: flex;
            gap: 10px;
        }

        .btn-edit, .btn-delete {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .btn-edit:hover {
            background: #e3f2fd;
        }

        .btn-delete:hover {
            background: #ffebee;
        }

        .no-tasks {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .no-tasks-icon {
            font-size: 64px;
            margin-bottom: 20px;
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
            background-color: rgba(0,0,0,0.5);
            overflow: auto;
        }

        .modal-content {
            background-color: white;
            margin: 3% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 700px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
        }

        .modal-header h2 {
            color: #333;
            font-size: 24px;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            background: none;
        }

        .close:hover {
            color: #000;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .task-types-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .weeks-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-top: 10px;
        }

        .days-container {
            display: none; /* إخفاء الأيام في البداية */
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            margin-top: 10px;
            width: 100%;
            box-sizing: border-box;
        }

        .days-container.visible {
            display: grid; /* إظهار الأيام عند اختيار الأسبوع */
        }

        .week-card, .day-card {
            padding: 12px 8px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            font-weight: 500;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 70px;
            box-sizing: border-box;
            width: 100%;
        }

        .day-card {
            min-height: 75px;
            padding: 10px 5px;
        }

        .day-card .day-name {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 4px;
            line-height: 1.2;
        }

        .day-card .day-number {
            font-size: 16px;
            font-weight: 700;
            color: #667eea;
            line-height: 1.2;
        }

        .day-card.selected .day-number {
            color: white;
        }

        .week-card:hover, .day-card:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.2);
        }

        .week-card.selected, .day-card.selected {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        @media (max-width: 768px) {
            .modal-content {
                padding: 20px;
                max-width: 95%;
            }
            
            .weeks-container {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
            
            .days-container {
                grid-template-columns: repeat(4, 1fr);
                gap: 6px;
            }
            
            .week-card, .day-card {
                padding: 10px 5px;
                min-height: 60px;
            }
            
            .day-card {
                min-height: 65px;
            }
            
            .day-card .day-name {
                font-size: 11px;
            }
            
            .day-card .day-number {
                font-size: 14px;
            }
        }

        .task-type-card {
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }

        .task-type-card:hover {
            border-color: #667eea;
            transform: translateY(-2px);
        }

        .task-type-card.selected {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .task-type-card .icon {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-save, .btn-cancel {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-save {
            background: #667eea;
            color: white;
        }

        .btn-save:hover {
            background: #5568d3;
        }

        .btn-cancel {
            background: #e0e0e0;
            color: #333;
        }

        .btn-cancel:hover {
            background: #d0d0d0;
        }

        @media (max-width: 768px) {
            .employee-tasks-container {
                padding: 20px;
            }

            .task-header {
                flex-direction: column;
            }

            .task-types-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="employee-tasks-container">
        <div class="employee-header">
            <h1>👤 {{ $employee->name }}</h1>
            <div class="month-display">
                الشهر: {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->locale('ar')->translatedFormat('F Y') }}
            </div>
        </div>

        @if($tasks->count() > 0)
        <div class="tasks-list">
            @foreach($tasks as $task)
            <div class="task-card" data-task-id="{{ $task->id }}">
                <div class="task-header">
                    <div>
                        <h3 class="task-title">{{ $task->title }}</h3>
                        @if($task->project)
                        <div class="task-project">{{ $task->project->name }}</div>
                        @endif
                    </div>
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

                @if($task->due_date)
                <div class="task-due-date">
                    📅 {{ \Carbon\Carbon::parse($task->due_date)->locale('ar')->translatedFormat('l، d F Y') }}
                </div>
                @endif

                <div class="subtasks-section">
                    <div class="subtasks-header">
                        <h3>المهام الفرعية</h3>
                        <button class="btn-add-subtask" data-parent-id="{{ $task->id }}">
                            ➕ إضافة مهمة فرعية
                        </button>
                    </div>
                    <div class="subtasks-list" id="subtasks-{{ $task->id }}">
                        @if($task->subtasks && $task->subtasks->count() > 0)
                            @foreach($task->subtasks as $subtask)
                            <div class="subtask-item" data-subtask-id="{{ $subtask->id }}">
                                <div class="subtask-info">
                                    <div class="subtask-title">{{ $subtask->title }}</div>
                                    @if($subtask->description)
                                    <p style="color: #666; font-size: 14px; margin-top: 5px;">{{ $subtask->description }}</p>
                                    @endif
                                    @if($subtask->task_types && count($subtask->task_types) > 0)
                                    <div class="task-types" style="margin-top: 8px;">
                                        @foreach($subtask->task_types as $type)
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
                                </div>
                                <div class="subtask-actions">
                                    <button class="btn-edit" onclick="editSubtask({{ $subtask->id }})" title="تعديل">✏️</button>
                                    <button class="btn-delete" onclick="deleteSubtask({{ $subtask->id }})" title="حذف">🗑️</button>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <p style="text-align: center; color: #999; padding: 20px;">لا توجد مهام فرعية</p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="no-tasks">
            <div class="no-tasks-icon">📋</div>
            <h2>لا توجد مهام لهذا الشهر</h2>
        </div>
        @endif
    </div>

    <!-- Modal for Add/Edit Subtask -->
    <div id="subtaskModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">إضافة مهمة فرعية</h2>
                <button class="close" onclick="closeModal()">&times;</button>
            </div>
            <form id="subtaskForm">
                <input type="hidden" id="subtaskId" name="subtask_id">
                <input type="hidden" id="parentId" name="parent_id">

                <div class="form-group">
                    <label for="subtaskTitle">عنوان المهمة *</label>
                    <input type="text" id="subtaskTitle" name="title" required>
                </div>

                <div class="form-group">
                    <label for="subtaskDescription">الوصف</label>
                    <textarea id="subtaskDescription" name="description"></textarea>
                </div>

                <div class="form-group">
                    <label>نوع المهمة</label>
                    <div class="task-types-container">
                        <div class="task-type-card" data-type="كتابة">
                            <div class="icon">✍️</div>
                            <div>كتابة</div>
                        </div>
                        <div class="task-type-card" data-type="فيديو">
                            <div class="icon">🎥</div>
                            <div>فيديو</div>
                        </div>
                        <div class="task-type-card" data-type="إعلان">
                            <div class="icon">📢</div>
                            <div>إعلان</div>
                        </div>
                        <div class="task-type-card" data-type="تقرير">
                            <div class="icon">📊</div>
                            <div>تقرير</div>
                        </div>
                        <div class="task-type-card" data-type="إعلان آخر">
                            <div class="icon">📰</div>
                            <div>إعلان آخر</div>
                        </div>
                        <div class="task-type-card" data-type="تصميم">
                            <div class="icon">🎨</div>
                            <div>تصميم</div>
                        </div>
                        <div class="task-type-card" data-type="نشر">
                            <div class="icon">📤</div>
                            <div>نشر</div>
                        </div>
                    </div>
                    <input type="hidden" id="taskTypesInput" name="task_types">
                </div>

                <div class="form-group">
                    <label>تاريخ الاستحقاق</label>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 10px; font-weight: 600; color: #333;">الأسبوع:</label>
                        <div class="weeks-container">
                            <div class="week-card" data-week="1">الأسبوع الأول</div>
                            <div class="week-card" data-week="2">الأسبوع الثاني</div>
                            <div class="week-card" data-week="3">الأسبوع الثالث</div>
                            <div class="week-card" data-week="4">الأسبوع الرابع</div>
                        </div>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 10px; font-weight: 600; color: #333;">اليوم:</label>
                        <div class="days-container">
                            <div class="day-card" data-day="0">
                                <div class="day-name">الأحد</div>
                                <div class="day-number" id="day-number-0">-</div>
                            </div>
                            <div class="day-card" data-day="1">
                                <div class="day-name">الإثنين</div>
                                <div class="day-number" id="day-number-1">-</div>
                            </div>
                            <div class="day-card" data-day="2">
                                <div class="day-name">الثلاثاء</div>
                                <div class="day-number" id="day-number-2">-</div>
                            </div>
                            <div class="day-card" data-day="3">
                                <div class="day-name">الأربعاء</div>
                                <div class="day-number" id="day-number-3">-</div>
                            </div>
                            <div class="day-card" data-day="4">
                                <div class="day-name">الخميس</div>
                                <div class="day-number" id="day-number-4">-</div>
                            </div>
                            <div class="day-card" data-day="5">
                                <div class="day-name">الجمعة</div>
                                <div class="day-number" id="day-number-5">-</div>
                            </div>
                            <div class="day-card" data-day="6">
                                <div class="day-name">السبت</div>
                                <div class="day-number" id="day-number-6">-</div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="subtaskDueDate" name="due_date">
                    <input type="hidden" id="selectedWeek" name="week">
                    <input type="hidden" id="selectedDay" name="day">
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">إلغاء</button>
                    <button type="submit" class="btn-save">حفظ</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let selectedTaskTypes = [];
        let selectedWeek = null;
        let selectedDay = null;
        const phone = '{{ $employee->phone }}';
        const selectedMonth = '{{ $selectedMonth }}'; // Format: YYYY-MM

        // Function to calculate date from week and day
        function calculateDateFromWeekAndDay(week, day) {
            if (!week || day === null) return null;
            
            // Parse the month
            const [year, month] = selectedMonth.split('-').map(Number);
            
            // Calculate the first day of the month
            const firstDay = new Date(year, month - 1, 1);
            const firstDayOfWeek = firstDay.getDay(); // 0 = Sunday, 6 = Saturday
            
            // Convert our day format (0 = Sunday, 6 = Saturday) to JavaScript day
            const jsDay = parseInt(day);
            
            // Calculate the start day of the week
            // Week 1: days 1-7, Week 2: days 8-14, Week 3: days 15-21, Week 4: days 22-28
            const weekStartDay = (week - 1) * 7 + 1;
            const firstDayInWeek = new Date(year, month - 1, weekStartDay).getDay();
            
            // Calculate the offset to get to the target day
            let dayOffset = jsDay - firstDayInWeek;
            if (dayOffset < 0) {
                dayOffset += 7;
            }
            
            const targetDate = weekStartDay + dayOffset;
            
            // Make sure the date is within the month
            const daysInMonth = new Date(year, month, 0).getDate();
            if (targetDate > daysInMonth || targetDate < 1) {
                return null; // Date is outside the month
            }
            
            const date = new Date(year, month - 1, targetDate);
            return date.toISOString().split('T')[0]; // Format: YYYY-MM-DD
        }

        // Function to update day numbers when week is selected
        function updateDayNumbers(week) {
            const daysContainer = document.querySelector('.days-container');
            
            if (!week) {
                // Reset all day numbers and hide days
                for (let i = 0; i <= 6; i++) {
                    const dayNumberEl = document.getElementById(`day-number-${i}`);
                    if (dayNumberEl) {
                        dayNumberEl.textContent = '-';
                    }
                }
                if (daysContainer) {
                    daysContainer.classList.remove('visible');
                }
                return;
            }

            // Parse the month
            const [year, month] = selectedMonth.split('-').map(Number);
            
            // Calculate the start day of the week
            const weekStartDay = (week - 1) * 7 + 1;
            const firstDayInWeek = new Date(year, month - 1, weekStartDay).getDay();
            
            // Calculate day numbers for each day of the week
            const daysInMonth = new Date(year, month, 0).getDate();
            
            // Store day data with numbers for sorting
            const daysData = [];
            
            for (let i = 0; i <= 6; i++) {
                const jsDay = i; // 0 = Sunday, 6 = Saturday
                let dayOffset = jsDay - firstDayInWeek;
                if (dayOffset < 0) {
                    dayOffset += 7;
                }
                
                const targetDate = weekStartDay + dayOffset;
                const dayNumberEl = document.getElementById(`day-number-${i}`);
                
                let dayNumber = '-';
                if (targetDate <= daysInMonth && targetDate >= 1) {
                    dayNumber = targetDate;
                }
                
                if (dayNumberEl) {
                    dayNumberEl.textContent = dayNumber;
                }
                
                // Store day data for sorting
                daysData.push({
                    day: i,
                    number: dayNumber === '-' ? 999 : parseInt(dayNumber), // Put invalid days at the end
                    element: document.querySelector(`.day-card[data-day="${i}"]`)
                });
            }
            
            // Sort days by number (date)
            daysData.sort((a, b) => a.number - b.number);
            
            // Reorder days in the container
            if (daysContainer) {
                daysData.forEach(dayData => {
                    if (dayData.element) {
                        daysContainer.appendChild(dayData.element);
                    }
                });
                
                // Show days container
                daysContainer.classList.add('visible');
            }
        }

        // Week Selection
        document.querySelectorAll('.week-card').forEach(card => {
            card.addEventListener('click', function() {
                // Remove selection from all weeks
                document.querySelectorAll('.week-card').forEach(c => c.classList.remove('selected'));
                // Select this week
                this.classList.add('selected');
                selectedWeek = parseInt(this.dataset.week);
                document.getElementById('selectedWeek').value = selectedWeek;
                
                // Update day numbers
                updateDayNumbers(selectedWeek);
                
                // Update date if day is already selected
                if (selectedDay !== null) {
                    const calculatedDate = calculateDateFromWeekAndDay(selectedWeek, selectedDay);
                    document.getElementById('subtaskDueDate').value = calculatedDate || '';
                }
            });
        });

        // Day Selection
        document.querySelectorAll('.day-card').forEach(card => {
            card.addEventListener('click', function() {
                // Remove selection from all days
                document.querySelectorAll('.day-card').forEach(c => c.classList.remove('selected'));
                // Select this day
                this.classList.add('selected');
                selectedDay = parseInt(this.dataset.day);
                document.getElementById('selectedDay').value = selectedDay;
                
                // Update date if week is already selected
                if (selectedWeek !== null) {
                    const calculatedDate = calculateDateFromWeekAndDay(selectedWeek, selectedDay);
                    document.getElementById('subtaskDueDate').value = calculatedDate || '';
                }
            });
        });

        // Task Type Selection
        document.querySelectorAll('.task-type-card').forEach(card => {
            card.addEventListener('click', function() {
                const type = this.dataset.type;
                if (this.classList.contains('selected')) {
                    this.classList.remove('selected');
                    selectedTaskTypes = selectedTaskTypes.filter(t => t !== type);
                } else {
                    this.classList.add('selected');
                    selectedTaskTypes.push(type);
                }
                document.getElementById('taskTypesInput').value = JSON.stringify(selectedTaskTypes);
            });
        });

        // Add Subtask
        document.querySelectorAll('.btn-add-subtask').forEach(btn => {
            btn.addEventListener('click', function() {
                const parentId = this.dataset.parentId;
                document.getElementById('parentId').value = parentId;
                document.getElementById('subtaskId').value = '';
                document.getElementById('subtaskTitle').value = '';
                document.getElementById('subtaskDescription').value = '';
                document.getElementById('subtaskDueDate').value = '';
                document.getElementById('selectedWeek').value = '';
                document.getElementById('selectedDay').value = '';
                document.getElementById('modalTitle').textContent = 'إضافة مهمة فرعية';
                selectedTaskTypes = [];
                selectedWeek = null;
                selectedDay = null;
                document.querySelectorAll('.task-type-card').forEach(c => c.classList.remove('selected'));
                document.querySelectorAll('.week-card').forEach(c => c.classList.remove('selected'));
                document.querySelectorAll('.day-card').forEach(c => c.classList.remove('selected'));
                document.getElementById('taskTypesInput').value = '';
                updateDayNumbers(null); // Reset day numbers and hide days
                document.getElementById('subtaskModal').style.display = 'block';
            });
        });

        // Edit Subtask
        async function editSubtask(subtaskId) {
            try {
                const response = await fetch(`/tasks/${subtaskId}`);
                const data = await response.json();
                const task = data.task;

                document.getElementById('subtaskId').value = task.id;
                document.getElementById('parentId').value = task.parent_id;
                document.getElementById('subtaskTitle').value = task.title;
                document.getElementById('subtaskDescription').value = task.description || '';
                
                // Calculate week and day from due_date
                if (task.due_date) {
                    const dueDate = new Date(task.due_date.split('T')[0]);
                    const dayOfMonth = dueDate.getDate();
                    const dayOfWeek = dueDate.getDay(); // 0 = Sunday, 6 = Saturday
                    
                    // Calculate week (1-4)
                    const week = Math.min(Math.ceil(dayOfMonth / 7), 4);
                    selectedWeek = week;
                    selectedDay = dayOfWeek;
                    
                    // Select week and day cards
                    document.querySelectorAll('.week-card').forEach(card => {
                        if (parseInt(card.dataset.week) === week) {
                            card.classList.add('selected');
                        } else {
                            card.classList.remove('selected');
                        }
                    });
                    
                    document.querySelectorAll('.day-card').forEach(card => {
                        if (parseInt(card.dataset.day) === dayOfWeek) {
                            card.classList.add('selected');
                        } else {
                            card.classList.remove('selected');
                        }
                    });
                    
                    document.getElementById('selectedWeek').value = week;
                    document.getElementById('selectedDay').value = dayOfWeek;
                    document.getElementById('subtaskDueDate').value = task.due_date.split('T')[0];
                    
                    // Update day numbers and show days (with sorting)
                    updateDayNumbers(week);
                } else {
                    selectedWeek = null;
                    selectedDay = null;
                    document.querySelectorAll('.week-card').forEach(c => c.classList.remove('selected'));
                    document.querySelectorAll('.day-card').forEach(c => c.classList.remove('selected'));
                    document.getElementById('selectedWeek').value = '';
                    document.getElementById('selectedDay').value = '';
                    document.getElementById('subtaskDueDate').value = '';
                    updateDayNumbers(null); // Reset day numbers
                }
                
                document.getElementById('modalTitle').textContent = 'تعديل مهمة فرعية';

                selectedTaskTypes = task.task_types || [];
                document.querySelectorAll('.task-type-card').forEach(card => {
                    if (selectedTaskTypes.includes(card.dataset.type)) {
                        card.classList.add('selected');
                    } else {
                        card.classList.remove('selected');
                    }
                });
                document.getElementById('taskTypesInput').value = JSON.stringify(selectedTaskTypes);

                document.getElementById('subtaskModal').style.display = 'block';
            } catch (error) {
                alert('حدث خطأ أثناء تحميل المهمة');
                console.error(error);
            }
        }

        // Delete Subtask
        async function deleteSubtask(subtaskId) {
            if (!confirm('هل أنت متأكد من حذف هذه المهمة الفرعية؟')) {
                return;
            }

            try {
                const response = await fetch(`/tasks/employee/${phone}/${subtaskId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();

                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'حدث خطأ أثناء حذف المهمة');
                }
            } catch (error) {
                alert('حدث خطأ أثناء حذف المهمة');
                console.error(error);
            }
        }

        // Submit Form
        document.getElementById('subtaskForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const subtaskId = document.getElementById('subtaskId').value;
            const formData = {
                parent_id: document.getElementById('parentId').value,
                title: document.getElementById('subtaskTitle').value,
                description: document.getElementById('subtaskDescription').value,
                due_date: document.getElementById('subtaskDueDate').value || null,
                task_types: selectedTaskTypes,
            };

            try {
                let response;
                if (subtaskId) {
                    // Update
                    response = await fetch(`/tasks/employee/${phone}/${subtaskId}`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(formData),
                    });
                } else {
                    // Create
                    response = await fetch(`/tasks/employee/${phone}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(formData),
                    });
                }

                const data = await response.json();

                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'حدث خطأ أثناء حفظ المهمة');
                }
            } catch (error) {
                alert('حدث خطأ أثناء حفظ المهمة');
                console.error(error);
            }
        });

        // Close Modal
        function closeModal() {
            document.getElementById('subtaskModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('subtaskModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>

