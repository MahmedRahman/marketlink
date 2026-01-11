<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Employee;
use App\Models\Project;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * عرض لوحة المهام
     */
    public function index(Request $request)
    {
        // الحصول على الشهر المحدد (افتراضي: الشهر الحالي)
        $selectedMonth = $request->get('month', date('Y-m'));
        
        // جلب جميع الموظفين مرتبين حسب order
        $employees = Employee::orderBy('order')->orderBy('name')->get();
        
        // جلب جميع المشاريع
        $projects = Project::orderBy('name')->get();
        
        // جلب جميع المهام الرئيسية فقط (بدون parent_id) للشهر المحدد مجمعة حسب الموظف
        $tasksByEmployee = Task::where('month', $selectedMonth)
            ->whereNull('parent_id')
            ->with(['employee', 'project', 'subtasks'])
            ->orderBy('order')
            ->get()
            ->groupBy('employee_id');
        
        // جلب جميع المهام الرئيسية فقط (بدون parent_id) للشهر المحدد مجمعة حسب المشروع
        $tasksByProject = Task::where('month', $selectedMonth)
            ->whereNull('parent_id')
            ->with(['employee', 'project', 'subtasks'])
            ->orderBy('order')
            ->get()
            ->groupBy('project_id');
        
        // جلب جميع المهام مع due_date للتقويم (فقط المهام الرئيسية)
        $tasksForCalendar = Task::where('month', $selectedMonth)
            ->whereNull('parent_id')
            ->whereNotNull('due_date')
            ->with(['employee', 'project', 'subtasks'])
            ->get()
            ->groupBy(function($task) {
                return \Carbon\Carbon::parse($task->due_date)->format('Y-m-d');
            })
            ->map(function($tasks, $date) {
                return [
                    'date' => $date,
                    'tasks' => $tasks->map(function($task) {
                        return [
                            'id' => $task->id,
                            'title' => $task->title,
                            'status' => $task->status,
                            'priority' => $task->priority,
                            'employee' => $task->employee ? $task->employee->name : null,
                            'project' => $task->project ? $task->project->name : null,
                        ];
                    })->toArray()
                ];
            })
            ->values()
            ->toArray();
        
        return view('tasks.index', compact('employees', 'projects', 'tasksByEmployee', 'tasksByProject', 'tasksForCalendar', 'selectedMonth'));
    }

    /**
     * عرض مهمة واحدة (للتعديل)
     */
    public function show(Task $task)
    {
        return response()->json([
            'task' => $task->load(['employee', 'project', 'subtasks', 'parent'])
        ]);
    }

    /**
     * إنشاء مهمة جديدة
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:tasks,id',
            'employee_id' => 'nullable|exists:employees,id',
            'project_id' => 'nullable|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,done',
            'priority' => 'nullable|integer|min:1|max:5',
            'due_date' => 'nullable|date',
            'month' => 'required|string',
            'tags' => 'nullable|string',
            'task_types' => 'nullable|array',
        ]);

        // تحويل tags من JSON string إلى array
        if (isset($validated['tags']) && !empty($validated['tags'])) {
            $validated['tags'] = json_decode($validated['tags'], true) ?? [];
        } else {
            $validated['tags'] = [];
        }

        // تحويل employee_id الفارغ إلى null
        if (empty($validated['employee_id'])) {
            $validated['employee_id'] = null;
        }

        // تحويل parent_id الفارغ إلى null
        if (isset($validated['parent_id']) && empty($validated['parent_id'])) {
            $validated['parent_id'] = null;
        }

        // إذا كانت مهمة فرعية، نسخ employee_id و project_id من المهمة الرئيسية
        if (!empty($validated['parent_id'])) {
            $parentTask = Task::find($validated['parent_id']);
            if ($parentTask) {
                $validated['employee_id'] = $parentTask->employee_id;
                $validated['project_id'] = $parentTask->project_id;
                $validated['month'] = $parentTask->month;
            }
        }

        // حساب الترتيب (آخر ترتيب + 1) حسب الموظف والحالة
        $query = Task::where('month', $validated['month'])
            ->where('status', $validated['status'])
            ->where('employee_id', $validated['employee_id']);
        
        // إذا كانت مهمة فرعية، نستخدم parent_id في حساب الترتيب
        if (!empty($validated['parent_id'])) {
            $query->where('parent_id', $validated['parent_id']);
        } else {
            $query->whereNull('parent_id');
        }
        
        $lastOrder = $query->max('order') ?? 0;
        $validated['order'] = $lastOrder + 1;

        $task = Task::create($validated);

        return response()->json([
            'success' => true,
            'task' => $task->load(['employee', 'project', 'subtasks', 'parent']),
            'message' => 'تم إنشاء المهمة بنجاح'
        ]);
    }

    /**
     * تحديث المهمة
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:tasks,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:todo,in_progress,done',
            'priority' => 'nullable|integer|min:1|max:5',
            'due_date' => 'nullable|date',
            'order' => 'nullable|integer',
            'project_id' => 'nullable|exists:projects,id',
            'employee_id' => 'nullable|exists:employees,id',
            'tags' => 'nullable|string',
            'task_types' => 'nullable|array',
        ]);

        // تحويل tags من JSON string إلى array
        if (isset($validated['tags']) && !empty($validated['tags'])) {
            $validated['tags'] = json_decode($validated['tags'], true) ?? [];
        } else {
            $validated['tags'] = [];
        }

        // تحويل employee_id الفارغ إلى null
        if (isset($validated['employee_id']) && empty($validated['employee_id'])) {
            $validated['employee_id'] = null;
        }

        // تحويل parent_id الفارغ إلى null
        if (isset($validated['parent_id']) && empty($validated['parent_id'])) {
            $validated['parent_id'] = null;
        }

        $task->update($validated);

        return response()->json([
            'success' => true,
            'task' => $task->load(['employee', 'project', 'subtasks', 'parent']),
            'message' => 'تم تحديث المهمة بنجاح'
        ]);
    }

    /**
     * حذف المهمة
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المهمة بنجاح'
        ]);
    }


    /**
     * تحديث ترتيب المهام (للسحب والإفلات)
     */
    public function updateOrder(Request $request)
    {
        $tasks = $request->input('tasks', []);

        foreach ($tasks as $taskData) {
            $updateData = [
                'order' => $taskData['order']
            ];
            
            // تحديث status إذا كان موجوداً
            if (isset($taskData['status'])) {
                $updateData['status'] = $taskData['status'];
            }
            
            Task::where('id', $taskData['id'])->update($updateData);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث ترتيب المهام بنجاح'
        ]);
    }

    /**
     * عرض مهام الموظف حسب رقم التليفون
     */
    public function employeeView($phone)
    {
        // فك تشفير رقم التليفون من الـ URL
        $phone = rawurldecode($phone);
        
        // تنظيف رقم التليفون (إزالة المسافات)
        $phone = trim($phone);
        
        // البحث عن الموظف برقم التليفون
        $employee = Employee::where('phone', $phone)->first();
        
        if (!$employee) {
            abort(404, 'الموظف غير موجود');
        }

        // الحصول على الشهر المحدد (افتراضي: الشهر الحالي)
        $selectedMonth = request()->get('month', date('Y-m'));
        
        // جلب جميع المهام الرئيسية للموظف في الشهر المحدد
        $tasks = Task::where('employee_id', $employee->id)
            ->where('month', $selectedMonth)
            ->whereNull('parent_id')
            ->with(['project', 'subtasks'])
            ->orderBy('order')
            ->get();

        return view('tasks.employee', compact('employee', 'tasks', 'selectedMonth'));
    }

    /**
     * إضافة مهمة فرعية من قبل الموظف
     */
    public function employeeStore(Request $request, $phone)
    {
        // فك تشفير رقم التليفون من الـ URL
        $phone = rawurldecode($phone);
        
        // تنظيف رقم التليفون (إزالة المسافات)
        $phone = trim($phone);
        
        // البحث عن الموظف برقم التليفون
        $employee = Employee::where('phone', $phone)->first();
        
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'الموظف غير موجود'
            ], 404);
        }

        $validated = $request->validate([
            'parent_id' => 'required|exists:tasks,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'task_types' => 'nullable|array',
        ]);

        // التحقق من أن المهمة الرئيسية تخص هذا الموظف
        $parentTask = Task::find($validated['parent_id']);
        if (!$parentTask || $parentTask->employee_id != $employee->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بإضافة مهمة فرعية لهذه المهمة'
            ], 403);
        }

        // تعيين القيم من المهمة الرئيسية
        $validated['employee_id'] = $employee->id;
        $validated['project_id'] = $parentTask->project_id;
        $validated['month'] = $parentTask->month;
        $validated['status'] = 'todo';
        $validated['priority'] = 1;

        // حساب الترتيب
        $lastOrder = Task::where('parent_id', $validated['parent_id'])
            ->max('order') ?? 0;
        $validated['order'] = $lastOrder + 1;

        $task = Task::create($validated);

        return response()->json([
            'success' => true,
            'task' => $task->load(['project', 'parent']),
            'message' => 'تم إضافة المهمة الفرعية بنجاح'
        ]);
    }

    /**
     * تحديث مهمة فرعية من قبل الموظف
     */
    public function employeeUpdate(Request $request, $phone, Task $task)
    {
        // فك تشفير رقم التليفون من الـ URL
        $phone = rawurldecode($phone);
        
        // تنظيف رقم التليفون (إزالة المسافات)
        $phone = trim($phone);
        
        // البحث عن الموظف برقم التليفون
        $employee = Employee::where('phone', $phone)->first();
        
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'الموظف غير موجود'
            ], 404);
        }

        // التحقق من أن المهمة تخص هذا الموظف وهي مهمة فرعية
        if ($task->employee_id != $employee->id || !$task->parent_id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتعديل هذه المهمة'
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'task_types' => 'nullable|array',
        ]);

        // تحويل task_types من array إلى JSON إذا لزم الأمر
        if (isset($validated['task_types']) && is_array($validated['task_types'])) {
            $validated['task_types'] = $validated['task_types'];
        } else {
            $validated['task_types'] = $task->task_types ?? [];
        }

        $task->update($validated);

        return response()->json([
            'success' => true,
            'task' => $task->load(['project', 'parent']),
            'message' => 'تم تحديث المهمة الفرعية بنجاح'
        ]);
    }

    /**
     * حذف مهمة فرعية من قبل الموظف
     */
    public function employeeDestroy($phone, Task $task)
    {
        // فك تشفير رقم التليفون من الـ URL
        $phone = rawurldecode($phone);
        
        // تنظيف رقم التليفون (إزالة المسافات)
        $phone = trim($phone);
        
        // البحث عن الموظف برقم التليفون
        $employee = Employee::where('phone', $phone)->first();
        
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'الموظف غير موجود'
            ], 404);
        }

        // التحقق من أن المهمة تخص هذا الموظف وهي مهمة فرعية
        if ($task->employee_id != $employee->id || !$task->parent_id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بحذف هذه المهمة'
            ], 403);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المهمة الفرعية بنجاح'
        ]);
    }
}
