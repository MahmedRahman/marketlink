<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Project;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * عرض جميع الموظفين (مع فلتر اختياري حسب شهر المشاريع)
     */
    public function index(Request $request)
    {
        $selectedMonth = $request->get('month'); // null = الكل

        $employeeIdsQuery = \DB::table('employee_project')
            ->join('projects', 'projects.id', '=', 'employee_project.project_id')
            ->select('employee_project.employee_id');

        if ($selectedMonth && preg_match('/^\d{4}-\d{2}$/', $selectedMonth)) {
            $employeeIdsQuery->where('projects.month', $selectedMonth);
        }

        $employeeIds = $employeeIdsQuery->distinct()->pluck('employee_id')->toArray();

        $employeesQuery = Employee::with(['projects' => function ($query) use ($selectedMonth) {
            $query->withPivot('service_types');
            if ($selectedMonth) {
                $query->where('month', $selectedMonth);
            }
        }])->orderBy('created_at', 'desc');

        if ($selectedMonth) {
            $employeesQuery->whereIn('id', $employeeIds ?: [0]);
        }

        $employees = $employeesQuery->get();

        $totalEmployees = $employees->count();
        $activeEmployees = $employees->where('status', 'active')->count();
        $inactiveEmployees = $employees->where('status', 'inactive')->count();
        $totalSalaries = $employees->sum('monthly_salary');

        // الأشهر التي فيها موظفون (مشاريع مرتبطة بموظفين) فقط
        $monthCounts = \DB::table('projects')
            ->join('employee_project', 'employee_project.project_id', '=', 'projects.id')
            ->whereNotNull('projects.month')
            ->where('projects.month', '!=', '')
            ->selectRaw('projects.month as month, COUNT(DISTINCT employee_project.employee_id) as cnt')
            ->groupBy('projects.month')
            ->orderBy('projects.month', 'desc')
            ->pluck('cnt', 'month')
            ->toArray();

        $allCount = Employee::count();

        return view('employees.index', compact(
            'employees', 'totalEmployees', 'activeEmployees', 'inactiveEmployees', 'totalSalaries',
            'selectedMonth', 'monthCounts', 'allCount'
        ));
    }

    /**
     * عرض نموذج إضافة موظف جديد
     */
    public function create()
    {
        $projects = Project::where('status', 'active')->orderBy('name')->get();
        return view('employees.create', compact('projects'));
    }

    /**
     * حفظ موظف جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'projects' => 'required|array|min:1',
            'projects.*' => 'exists:projects,id',
            'service_types' => 'required|array',
            'service_types.*' => 'array',
            'service_types.*.*' => 'in:full_management,media_buy,design,videos,publishing,programming,part_time',
            'status' => 'required|in:active,inactive',
            'monthly_salary' => 'nullable|numeric|min:0',
        ]);

        $employee = Employee::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'status' => $validated['status'],
            'monthly_salary' => $validated['monthly_salary'] ?? null,
        ]);

        // ربط الموظف بالمشاريع مع الخدمات
        $syncData = [];
        foreach ($validated['projects'] as $projectId) {
            $syncData[$projectId] = [
                'service_types' => json_encode($validated['service_types'][$projectId] ?? [])
            ];
        }
        $employee->projects()->sync($syncData);

        return redirect()->route('employees.index')
            ->with('success', 'تم إضافة الموظف بنجاح');
    }

    /**
     * عرض نموذج تعديل موظف
     */
    public function edit(Employee $employee)
    {
        $projects = Project::where('status', 'active')->orderBy('name')->get();
        $employee->load('projects');
        return view('employees.edit', compact('employee', 'projects'));
    }

    /**
     * تحديث بيانات موظف
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'projects' => 'required|array|min:1',
            'projects.*' => 'exists:projects,id',
            'service_types' => 'required|array',
            'service_types.*' => 'array',
            'service_types.*.*' => 'in:full_management,media_buy,design,videos,publishing,programming,part_time',
            'status' => 'required|in:active,inactive',
            'monthly_salary' => 'nullable|numeric|min:0',
        ]);

        $employee->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'status' => $validated['status'],
            'monthly_salary' => $validated['monthly_salary'] ?? null,
        ]);

        // تحديث ربط الموظف بالمشاريع مع الخدمات
        $syncData = [];
        foreach ($validated['projects'] as $projectId) {
            $syncData[$projectId] = [
                'service_types' => json_encode($validated['service_types'][$projectId] ?? [])
            ];
        }
        $employee->projects()->sync($syncData);

        return redirect()->route('employees.index')
            ->with('success', 'تم تحديث بيانات الموظف بنجاح');
    }

    /**
     * حذف موظف
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'تم حذف الموظف بنجاح');
    }

    /**
     * نسخ الموظفين المحددين إلى شهر آخر (نسخ مشاريعهم إلى الشهر وربطهم بها)
     */
    public function moveToMonth(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:employees,id',
            'month' => 'required|string|size:7|regex:/^\d{4}-\d{2}$/',
        ]);

        $employeeIds = array_filter($validated['ids']);
        if (empty($employeeIds)) {
            return redirect()->route('employees.index')
                ->with('error', 'يرجى اختيار موظف واحد على الأقل.');
        }

        $targetMonth = $validated['month'];
        $employees = Employee::with(['projects' => fn ($q) => $q->withPivot('service_types')])
            ->whereIn('id', $employeeIds)
            ->get();

        $projectIds = $employees->pluck('projects')->flatten()->pluck('id')->unique()->values()->all();
        if (empty($projectIds)) {
            return redirect()->route('employees.index')
                ->with('error', 'الموظفون المحددون لا يملكون مشاريع لنسخها.');
        }

        $projectMap = [];
        foreach (Project::with('employees')->whereIn('id', $projectIds)->get() as $project) {
            $newProject = $project->replicate();
            $newProject->month = $targetMonth;
            $newProject->save();
            $projectMap[$project->id] = $newProject;
        }

        foreach ($employees as $employee) {
            foreach ($employee->projects as $project) {
                $newProject = $projectMap[$project->id] ?? null;
                if (!$newProject) {
                    continue;
                }
                $serviceTypes = $project->pivot->service_types;
                if (is_string($serviceTypes)) {
                    $serviceTypes = json_decode($serviceTypes, true) ?: [];
                }
                $newProject->employees()->syncWithoutDetaching([
                    $employee->id => ['service_types' => json_encode($serviceTypes)],
                ]);
            }
        }

        $countProjects = count($projectMap);
        return redirect()->route('employees.index')
            ->with('success', "تم نسخ الموظفين المحددين إلى الشهر: تم إنشاء {$countProjects} مشروع جديد وربطهم بها.");
    }

    /**
     * تحديث ترتيب الموظفين (للسحب والإفلات)
     */
    public function updateOrder(Request $request)
    {
        $employees = $request->input('employees', []);

        foreach ($employees as $index => $employeeData) {
            Employee::where('id', $employeeData['id'])->update([
                'order' => $index + 1
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث ترتيب الموظفين بنجاح'
        ]);
    }
}
