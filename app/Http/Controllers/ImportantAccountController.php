<?php

namespace App\Http\Controllers;

use App\Models\ImportantAccount;
use App\Models\Employee;
use Illuminate\Http\Request;

class ImportantAccountController extends Controller
{
    public function index(Request $request)
    {
        $selectedMonth = $request->get('month');

        $query = ImportantAccount::with('employees');
        if ($selectedMonth && preg_match('/^\d{4}-\d{2}$/', $selectedMonth)) {
            $query->where('month', $selectedMonth);
        }
        $accounts = $query->orderBy('month', 'desc')->orderBy('site_name')->get();

        $monthCounts = ImportantAccount::selectRaw('month, count(*) as cnt')
            ->whereNotNull('month')
            ->where('month', '!=', '')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->pluck('cnt', 'month')
            ->toArray();

        $allCount = ImportantAccount::count();

        return view('important-accounts.index', compact('accounts', 'selectedMonth', 'monthCounts', 'allCount'));
    }

    public function create()
    {
        $employees = Employee::orderBy('name')->get();
        return view('important-accounts.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_url' => 'nullable|string|max:500',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'month' => 'nullable|string|size:7|regex:/^\d{4}-\d{2}$/',
            'employees' => 'nullable|array',
            'employees.*' => 'exists:employees,id',
        ]);

        $employees = $validated['employees'] ?? [];
        unset($validated['employees']);
        if (empty($validated['month'] ?? null)) {
            $validated['month'] = date('Y-m');
        }

        $account = ImportantAccount::create($validated);
        $account->employees()->sync($employees);

        return redirect()->route('important-accounts.index')
            ->with('success', 'تمت إضافة الحساب بنجاح');
    }

    public function edit(ImportantAccount $important_account)
    {
        $important_account->load('employees');
        $employees = Employee::orderBy('name')->get();
        return view('important-accounts.edit', compact('important_account', 'employees'));
    }

    public function update(Request $request, ImportantAccount $important_account)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_url' => 'nullable|string|max:500',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'month' => 'nullable|string|size:7|regex:/^\d{4}-\d{2}$/',
            'employees' => 'nullable|array',
            'employees.*' => 'exists:employees,id',
        ]);

        $employees = $validated['employees'] ?? [];
        unset($validated['employees']);
        if (array_key_exists('month', $validated) && ($validated['month'] ?? '') === '') {
            $validated['month'] = date('Y-m');
        }

        $important_account->update($validated);
        $important_account->employees()->sync($employees);

        return redirect()->route('important-accounts.index')
            ->with('success', 'تم تحديث الحساب بنجاح');
    }

    /**
     * نسخ حسابات محددة إلى شهر آخر (إنشاء نسخ جديدة)
     */
    public function moveToMonth(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:important_accounts,id',
            'month' => 'required|string|size:7|regex:/^\d{4}-\d{2}$/',
        ]);

        $ids = array_filter($validated['ids']);
        if (empty($ids)) {
            return redirect()->route('important-accounts.index')
                ->with('error', 'يرجى اختيار حساب واحد على الأقل.');
        }

        $targetMonth = $validated['month'];
        $count = 0;

        foreach (ImportantAccount::with('employees')->whereIn('id', $ids)->get() as $account) {
            $newAccount = $account->replicate();
            $newAccount->month = $targetMonth;
            $newAccount->save();
            $newAccount->employees()->sync($account->employees->pluck('id')->toArray());
            $count++;
        }

        return redirect()->route('important-accounts.index')
            ->with('success', "تم إنشاء {$count} حساب جديد بالشهر المحدد (مع الإبقاء على الأصل).");
    }

    public function destroy(ImportantAccount $important_account)
    {
        $important_account->delete();

        return redirect()->route('important-accounts.index')
            ->with('success', 'تم حذف الحساب بنجاح');
    }
}
