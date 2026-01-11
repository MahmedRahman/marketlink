<?php

namespace App\Http\Controllers;

use App\Models\FinancialRecord;
use Illuminate\Http\Request;

class FinancialRecordController extends Controller
{
    /**
     * عرض سجل محدد
     */
    public function show(FinancialRecord $financialRecord)
    {
        return response()->json($financialRecord);
    }

    /**
     * حفظ سجل جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:revenue,expense',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|in:usd,egp',
            'payment_status' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'record_date' => 'required|date',
            'month' => 'required|string',
        ]);

        $record = FinancialRecord::create($validated);

        return response()->json($record, 201);
    }

    /**
     * تحديث سجل
     */
    public function update(Request $request, FinancialRecord $financialRecord)
    {
        $validated = $request->validate([
            'type' => 'required|in:revenue,expense',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|in:usd,egp',
            'payment_status' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'record_date' => 'required|date',
            'month' => 'nullable|string',
        ]);

        $financialRecord->update($validated);

        return response()->json($financialRecord);
    }

    /**
     * حذف سجل
     */
    public function destroy(FinancialRecord $financialRecord)
    {
        $financialRecord->delete();

        return response()->json(['message' => 'تم حذف السجل بنجاح']);
    }
}
