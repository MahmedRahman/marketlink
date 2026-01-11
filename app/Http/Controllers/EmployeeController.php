<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Project;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * عرض جميع الموظفين
     */
    public function index()
    {
        $employees = Employee::with(['projects' => function($query) {
            $query->withPivot('service_types');
        }])->orderBy('created_at', 'desc')->get();
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', 'active')->count();
        $inactiveEmployees = Employee::where('status', 'inactive')->count();
        
        // حساب إجمالي المرتبات
        $totalSalaries = Employee::whereNotNull('monthly_salary')->sum('monthly_salary');
        
        return view('employees.index', compact('employees', 'totalEmployees', 'activeEmployees', 'inactiveEmployees', 'totalSalaries'));
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
