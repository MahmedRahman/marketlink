<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Employee;
use App\Models\Subscription;
use App\Models\FinancialRecord;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * عرض تقرير الحسابات
     */
    public function index(Request $request)
    {
        // الحصول على الشهر المحدد (افتراضي: الشهر الحالي)
        $selectedMonth = $request->get('month', date('Y-m'));
        $year = date('Y', strtotime($selectedMonth . '-01'));
        $month = date('m', strtotime($selectedMonth . '-01'));

        // حساب إجمالي إيرادات المشاريع (للمشاريع المنشأة في الشهر المحدد)
        $projects = Project::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();
        
        $totalProjectsRevenue = 0;
        foreach ($projects as $project) {
            if ($project->service_revenue && is_array($project->service_revenue)) {
                foreach ($project->service_revenue as $revenue) {
                    if ($revenue !== null && is_numeric($revenue)) {
                        $totalProjectsRevenue += $revenue;
                    }
                }
            }
        }

        // حساب إجمالي مرتبات الموظفين (للموظفين المنشأين في الشهر المحدد)
        $totalSalaries = Employee::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->whereNotNull('monthly_salary')
            ->sum('monthly_salary');

        // حساب إجمالي الاشتراكات (للاشتراكات المنشأة في الشهر المحدد)
        $totalSubscriptionsUSD = Subscription::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('currency', 'usd')
            ->whereNotNull('amount')
            ->sum('amount');
        
        $totalSubscriptionsEGP = Subscription::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('currency', 'egp')
            ->whereNotNull('amount')
            ->sum('amount');

        // حساب صافي الربح (الإيرادات - المرتبات - الاشتراكات)
        $netProfit = $totalProjectsRevenue - $totalSalaries - $totalSubscriptionsEGP;

        // جلب السجلات التفصيلية
        $projectRecords = $projects->map(function($project) {
            $revenue = 0;
            if ($project->service_revenue && is_array($project->service_revenue)) {
                foreach ($project->service_revenue as $rev) {
                    if ($rev !== null && is_numeric($rev)) {
                        $revenue += $rev;
                    }
                }
            }
            return [
                'type' => 'إيراد',
                'description' => 'مشروع: ' . $project->name,
                'amount' => $revenue,
                'currency' => 'egp',
                'date' => $project->created_at->format('Y-m-d'),
            ];
        })->filter(function($record) {
            return $record['amount'] > 0;
        });

        $employeeRecords = Employee::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->whereNotNull('monthly_salary')
            ->get()
            ->map(function($employee) {
                return [
                    'type' => 'مصروف',
                    'description' => 'مرتب: ' . $employee->name,
                    'amount' => $employee->monthly_salary,
                    'currency' => 'egp',
                    'date' => $employee->created_at->format('Y-m-d'),
                ];
            });

        $subscriptionRecords = Subscription::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->whereNotNull('amount')
            ->get()
            ->map(function($subscription) {
                return [
                    'type' => 'مصروف',
                    'description' => 'اشتراك: ' . $subscription->site_name,
                    'amount' => $subscription->amount,
                    'currency' => $subscription->currency,
                    'date' => $subscription->created_at->format('Y-m-d'),
                ];
            });

        // دمج جميع السجلات وترتيبها حسب التاريخ
        $allRecords = $projectRecords->concat($employeeRecords)->concat($subscriptionRecords)
            ->sortBy('date')
            ->values();

        // جلب السجلات المالية المحفوظة للشهر المحدد
        $financialRecords = FinancialRecord::where('month', $selectedMonth)
            ->orderBy('record_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // حساب الإجماليات من السجلات المالية (مع تحويل العملة)
        // سعر الصرف: 1 USD = 50 EGP (يمكن تعديله لاحقاً)
        $exchangeRate = 50;
        
        $recordsRevenue = 0;
        $recordsExpenses = 0;
        $revenuePaid = 0;      // الإيرادات المحصلة
        $revenuePending = 0;   // الإيرادات المعلقة
        $expensesPaid = 0;     // المصروفات المدفوعة
        $expensesPending = 0;  // المصروفات المعلقة
        
        foreach ($financialRecords->where('type', 'revenue') as $record) {
            $amount = $record->currency === 'usd' ? $record->amount * $exchangeRate : $record->amount;
            $recordsRevenue += $amount;
            
            // تقسيم حسب حالة الدفع
            if (in_array(strtolower($record->payment_status ?? ''), ['paid', 'محصل', 'مدفوع'])) {
                $revenuePaid += $amount;
            } else {
                $revenuePending += $amount;
            }
        }
        
        foreach ($financialRecords->where('type', 'expense') as $record) {
            $amount = $record->currency === 'usd' ? $record->amount * $exchangeRate : $record->amount;
            $recordsExpenses += $amount;
            
            // تقسيم حسب الحالة
            if (in_array(strtolower($record->status ?? ''), ['paid', 'مدفوع', 'تم الدفع'])) {
                $expensesPaid += $amount;
            } else {
                $expensesPending += $amount;
            }
        }
        
        $recordsNetProfit = $recordsRevenue - $recordsExpenses;

        return view('reports.index', compact(
            'totalProjectsRevenue',
            'totalSalaries',
            'totalSubscriptionsUSD',
            'totalSubscriptionsEGP',
            'netProfit',
            'selectedMonth',
            'allRecords',
            'financialRecords',
            'recordsRevenue',
            'recordsExpenses',
            'recordsNetProfit',
            'revenuePaid',
            'revenuePending',
            'expensesPaid',
            'expensesPending'
        ));
    }

    /**
     * إنشاء السجلات المالية من البيانات الموجودة
     */
    public function createRecords(Request $request)
    {
        $selectedMonth = $request->input('month', date('Y-m'));
        
        // التحقق من صحة الشهر
        if (!preg_match('/^\d{4}-\d{2}$/', $selectedMonth)) {
            return response()->json([
                'success' => false,
                'message' => 'صيغة الشهر غير صحيحة'
            ], 400);
        }
        
        $year = date('Y', strtotime($selectedMonth . '-01'));
        $month = date('m', strtotime($selectedMonth . '-01'));

        // التحقق من وجود سجلات للشهر
        $existingRecords = FinancialRecord::where('month', $selectedMonth)->count();
        if ($existingRecords > 0) {
            return response()->json([
                'success' => false,
                'message' => 'يوجد بالفعل سجلات لهذا الشهر. يرجى حذفها أولاً أو تعديلها.'
            ], 400);
        }

        // إنشاء سجلات الإيرادات من المشاريع
        $projects = Project::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();
        
        foreach ($projects as $project) {
            if ($project->service_revenue && is_array($project->service_revenue)) {
                $totalRevenue = 0;
                foreach ($project->service_revenue as $revenue) {
                    if ($revenue !== null && is_numeric($revenue)) {
                        $totalRevenue += $revenue;
                    }
                }
                if ($totalRevenue > 0) {
                    FinancialRecord::create([
                        'type' => 'revenue',
                        'description' => 'مشروع: ' . $project->name,
                        'amount' => $totalRevenue,
                        'currency' => 'egp',
                        'payment_status' => 'pending', // حالة افتراضية
                        'record_date' => $project->created_at->format('Y-m-d'),
                        'month' => $selectedMonth,
                    ]);
                }
            }
        }

        // إنشاء سجلات المرتبات
        $employees = Employee::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->whereNotNull('monthly_salary')
            ->get();
        
        foreach ($employees as $employee) {
            FinancialRecord::create([
                'type' => 'expense',
                'description' => 'مرتب: ' . $employee->name,
                'amount' => $employee->monthly_salary,
                'currency' => 'egp',
                'status' => 'pending', // حالة افتراضية
                'record_date' => $employee->created_at->format('Y-m-d'),
                'month' => $selectedMonth,
            ]);
        }

        // إنشاء سجلات الاشتراكات
        $subscriptions = Subscription::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->whereNotNull('amount')
            ->get();
        
        foreach ($subscriptions as $subscription) {
            FinancialRecord::create([
                'type' => 'expense',
                'description' => 'اشتراك: ' . $subscription->site_name,
                'amount' => $subscription->amount,
                'currency' => $subscription->currency,
                'status' => 'pending', // حالة افتراضية
                'record_date' => $subscription->created_at->format('Y-m-d'),
                'month' => $selectedMonth,
            ]);
        }

        $revenueCount = 0;
        foreach ($projects as $project) {
            if ($project->service_revenue && is_array($project->service_revenue)) {
                $totalRevenue = 0;
                foreach ($project->service_revenue as $revenue) {
                    if ($revenue !== null && is_numeric($revenue)) {
                        $totalRevenue += $revenue;
                    }
                }
                if ($totalRevenue > 0) {
                    $revenueCount++;
                }
            }
        }

        $totalCount = $revenueCount + $employees->count() + $subscriptions->count();

        return response()->json([
            'success' => true,
            'message' => "تم إنشاء {$totalCount} سجل بنجاح"
        ]);
    }
}
