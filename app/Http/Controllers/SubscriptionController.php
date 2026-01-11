<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * عرض جميع الاشتراكات
     */
    public function index()
    {
        $subscriptions = Subscription::orderBy('created_at', 'desc')->get();
        $totalSubscriptions = Subscription::count();
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        $inactiveSubscriptions = Subscription::where('status', 'inactive')->count();
        $monthlySubscriptions = Subscription::where('subscription_type', 'monthly')->count();
        $yearlySubscriptions = Subscription::where('subscription_type', 'yearly')->count();
        
        // حساب إجمالي المبالغ حسب العملة
        $totalAmountUSD = Subscription::where('currency', 'usd')
            ->whereNotNull('amount')
            ->sum('amount');
        
        $totalAmountEGP = Subscription::where('currency', 'egp')
            ->whereNotNull('amount')
            ->sum('amount');
        
        // الإجمالي العام (يمكن تحويل الدولار للمصري أو عرضهما منفصلين)
        $totalAmount = $totalAmountUSD + $totalAmountEGP;
        
        return view('subscriptions.index', compact('subscriptions', 'totalSubscriptions', 'activeSubscriptions', 'inactiveSubscriptions', 'monthlySubscriptions', 'yearlySubscriptions', 'totalAmountUSD', 'totalAmountEGP', 'totalAmount'));
    }

    /**
     * عرض نموذج إضافة اشتراك جديد
     */
    public function create()
    {
        return view('subscriptions.create');
    }

    /**
     * حفظ اشتراك جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_url' => 'nullable|url|max:255',
            'login_type' => 'required|in:user_pass,gmail',
            'username' => 'nullable|string|max:255|required_if:login_type,user_pass',
            'password' => 'nullable|string|max:255|required_if:login_type,user_pass',
            'email' => 'nullable|email|max:255|required_if:login_type,gmail',
            'subscription_ownership' => 'required|in:official,shared',
            'shared_with_phone' => 'nullable|string|max:20|required_if:subscription_ownership,shared',
            'subscription_type' => 'required|in:monthly,yearly',
            'renewal_day' => 'nullable|integer|min:1|max:31|required_if:subscription_type,monthly',
            'amount' => 'nullable|numeric|min:0',
            'currency' => 'required|in:usd,egp',
            'status' => 'required|in:active,inactive',
        ]);

        // حفظ الإيميل في username إذا كان login_type = gmail
        if ($validated['login_type'] === 'gmail' && isset($validated['email'])) {
            $validated['username'] = $validated['email'];
            unset($validated['email']);
        }

        Subscription::create($validated);

        return redirect()->route('subscriptions.index')
            ->with('success', 'تم إضافة الاشتراك بنجاح');
    }

    /**
     * عرض نموذج تعديل اشتراك
     */
    public function edit(Subscription $subscription)
    {
        return view('subscriptions.edit', compact('subscription'));
    }

    /**
     * تحديث بيانات اشتراك
     */
    public function update(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_url' => 'nullable|url|max:255',
            'login_type' => 'required|in:user_pass,gmail',
            'username' => 'nullable|string|max:255|required_if:login_type,user_pass',
            'password' => 'nullable|string|max:255|required_if:login_type,user_pass',
            'email' => 'nullable|email|max:255|required_if:login_type,gmail',
            'subscription_ownership' => 'required|in:official,shared',
            'shared_with_phone' => 'nullable|string|max:20|required_if:subscription_ownership,shared',
            'subscription_type' => 'required|in:monthly,yearly',
            'renewal_day' => 'nullable|integer|min:1|max:31|required_if:subscription_type,monthly',
            'amount' => 'nullable|numeric|min:0',
            'currency' => 'required|in:usd,egp',
            'status' => 'required|in:active,inactive',
        ]);

        // حفظ الإيميل في username إذا كان login_type = gmail
        if ($validated['login_type'] === 'gmail' && isset($validated['email'])) {
            $validated['username'] = $validated['email'];
            unset($validated['email']);
        }

        $subscription->update($validated);

        return redirect()->route('subscriptions.index')
            ->with('success', 'تم تحديث بيانات الاشتراك بنجاح');
    }

    /**
     * حذف اشتراك
     */
    public function destroy(Subscription $subscription)
    {
        $subscription->delete();

        return redirect()->route('subscriptions.index')
            ->with('success', 'تم حذف الاشتراك بنجاح');
    }
}
