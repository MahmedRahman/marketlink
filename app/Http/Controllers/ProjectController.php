<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Customer;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * عرض جميع المشاريع
     */
    public function index()
    {
        $projects = Project::with('customer')->orderBy('created_at', 'desc')->get();
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', 'active')->count();
        $inactiveProjects = Project::where('status', 'inactive')->count();
        
        // حساب إجمالي الإيرادات
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
        
        return view('projects.index', compact('projects', 'totalProjects', 'activeProjects', 'inactiveProjects', 'totalRevenue'));
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
        ]);

        // معالجة service_revenue - إنشاء array لكل خدمة
        $serviceRevenue = [];
        if ($request->has('service_revenue')) {
            foreach ($request->service_type as $service) {
                $serviceRevenue[$service] = $request->service_revenue[$service] ?? null;
            }
        }
        $validated['service_revenue'] = $serviceRevenue;

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
        ]);

        // معالجة service_revenue - إنشاء array لكل خدمة
        $serviceRevenue = [];
        if ($request->has('service_revenue')) {
            foreach ($request->service_type as $service) {
                $serviceRevenue[$service] = $request->service_revenue[$service] ?? null;
            }
        }
        $validated['service_revenue'] = $serviceRevenue;

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
}
