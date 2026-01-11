<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('financial_records', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'revenue' or 'expense'
            $table->string('description'); // وصف السجل
            $table->decimal('amount', 10, 2); // المبلغ
            $table->enum('currency', ['usd', 'egp'])->default('egp'); // العملة
            $table->string('payment_status')->nullable(); // حالة الدفع (للإيرادات)
            $table->string('status')->nullable(); // الحالة (للمصروفات)
            $table->date('record_date'); // تاريخ السجل
            $table->string('month'); // الشهر (Y-m format)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_records');
    }
};
