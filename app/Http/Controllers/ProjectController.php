<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Customer;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * عرض جميع المشاريع (مع فلتر اختياري حسب الشهر)
     */
    public function index(Request $request)
    {
        $selectedMonth = $request->get('month'); // null = الكل

        $query = Project::with('customer');
        if ($selectedMonth && preg_match('/^\d{4}-\d{2}$/', $selectedMonth)) {
            $query->where('month', $selectedMonth);
        }
        $projects = $query->orderBy('month', 'desc')->orderBy('name')->get();

        $totalProjects = $projects->count();
        $activeProjects = $projects->where('status', 'active')->count();
        $inactiveProjects = $projects->where('status', 'inactive')->count();

        $totalRevenue = 0;
        foreach ($projects as $project) {
            if ($project->service_revenue && is_array($project->service_revenue)) {
                foreach ($project->service_revenue as $revenue) {
                    if ($revenue !== null && is_numeric($revenue)) {
                        $totalRevenue += $revenue;
                    }
                }
            }
        }

        // الأشهر التي فيها مشاريع فقط (مرتبة من الأحدث للأقدم)
        $monthCounts = Project::selectRaw('month, count(*) as cnt')
            ->whereNotNull('month')
            ->where('month', '!=', '')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->pluck('cnt', 'month')
            ->toArray();

        $allCount = Project::count();

        return view('projects.index', compact(
            'projects', 'totalProjects', 'activeProjects', 'inactiveProjects', 'totalRevenue',
            'selectedMonth', 'monthCounts', 'allCount'
        ));
    }

    /**
     * عرض نموذج إضافة مشروع جديد
     */
    public function create()
    {
        $customers = Customer::where('status', 'active')->orderBy('name')->get();
        return view('projects.create', compact('customers'));
    }

    /**
     * حفظ مشروع جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'name' => 'required|string|max:255',
            'service_type' => 'required|array',
            'service_type.*' => 'in:full_management,media_buy,design,videos,publishing,programming,part_time',
            'service_revenue' => 'nullable|array',
            'service_revenue.*' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'month' => 'nullable|string|size:7|regex:/^\d{4}-\d{2}$/',
        ]);

        // معالجة service_revenue - إنشاء array لكل خدمة
        $serviceRevenue = [];
        if ($request->has('service_revenue')) {
            foreach ($request->service_type as $service) {
                $serviceRevenue[$service] = $request->service_revenue[$service] ?? null;
            }
        }
        $validated['service_revenue'] = $serviceRevenue;
        $validated['month'] = $validated['month'] ?? date('Y-m');

        Project::create($validated);

        return redirect()->route('projects.index')
            ->with('success', 'تم إضافة المشروع بنجاح');
    }

    /**
     * عرض نموذج تعديل مشروع
     */
    public function edit(Project $project)
    {
        $customers = Customer::where('status', 'active')->orderBy('name')->get();
        return view('projects.edit', compact('project', 'customers'));
    }

    /**
     * تحديث بيانات مشروع
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'name' => 'required|string|max:255',
            'service_type' => 'required|array',
            'service_type.*' => 'in:full_management,media_buy,design,videos,publishing,programming,part_time',
            'service_revenue' => 'nullable|array',
            'service_revenue.*' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'month' => 'nullable|string|size:7|regex:/^\d{4}-\d{2}$/',
        ]);

        // معالجة service_revenue - إنشاء array لكل خدمة
        $serviceRevenue = [];
        if ($request->has('service_revenue')) {
            foreach ($request->service_type as $service) {
                $serviceRevenue[$service] = $request->service_revenue[$service] ?? null;
            }
        }
        $validated['service_revenue'] = $serviceRevenue;
        if (array_key_exists('month', $validated) && $validated['month'] === '') {
            $validated['month'] = date('Y-m');
        }

        $project->update($validated);

        return redirect()->route('projects.index')
            ->with('success', 'تم تحديث بيانات المشروع بنجاح');
    }

    /**
     * حذف مشروع
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'تم حذف المشروع بنجاح');
    }

    /**
     * نسخ مشاريع محددة إلى شهر آخر (الإبقاء على الأصل وإنشاء نسخة جديدة بالشهر المختار)
     */
    public function moveToMonth(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:projects,id',
            'month' => 'required|string|size:7|regex:/^\d{4}-\d{2}$/',
        ]);

        $ids = array_filter($validated['ids']);
        if (empty($ids)) {
            return redirect()->route('projects.index')
                ->with('error', 'يرجى اختيار مشروع واحد على الأقل.');
        }

        $targetMonth = $validated['month'];
        $count = 0;

        foreach (Project::with('employees')->whereIn('id', $ids)->get() as $project) {
            $newProject = $project->replicate();
            $newProject->month = $targetMonth;
            $newProject->save();

            if ($project->employees->isNotEmpty()) {
                $pivotData = [];
                foreach ($project->employees as $emp) {
                    $serviceTypes = $emp->pivot->service_types ?? [];
                    if (is_string($serviceTypes)) {
                        $serviceTypes = json_decode($serviceTypes, true) ?: [];
                    }
                    $pivotData[$emp->id] = ['service_types' => json_encode($serviceTypes)];
                }
                $newProject->employees()->attach($pivotData);
            }
            $count++;
        }

        return redirect()->route('projects.index')
            ->with('success', "تم إنشاء {$count} مشروع جديد بالشهر المحدد (مع الإبقاء على الأصل).");
    }
}
