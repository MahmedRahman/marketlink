<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * عرض جميع العملاء
     */
    public function index()
    {
        $customers = Customer::orderBy('created_at', 'desc')->get();
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('status', 'active')->count();
        $inactiveCustomers = Customer::where('status', 'inactive')->count();
        
        return view('customers.index', compact('customers', 'totalCustomers', 'activeCustomers', 'inactiveCustomers'));
    }

    /**
     * عرض نموذج إضافة عميل جديد
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * حفظ عميل جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'تم إضافة العميل بنجاح');
    }

    /**
     * عرض نموذج تعديل عميل
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * تحديث بيانات عميل
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', 'تم تحديث بيانات العميل بنجاح');
    }

    /**
     * حذف عميل
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'تم حذف العميل بنجاح');
    }

    /**
     * API: عرض جميع العملاء (للاستخدام في AJAX)
     */
    public function apiIndex()
    {
        $customers = Customer::orderBy('created_at', 'desc')->get();
        return response()->json($customers);
    }
}
