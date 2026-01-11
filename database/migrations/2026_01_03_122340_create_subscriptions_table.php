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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('site_name'); // اسم الموقع
            $table->string('username'); // اليوزر
            $table->string('password'); // الباسورد
            $table->string('responsible_phone'); // رقم تليفون الشخص المسؤول
            $table->date('renewal_date'); // تاريخ التجديد
            $table->enum('subscription_type', ['monthly', 'yearly'])->default('monthly'); // نوع الاشتراك (شهري/سنوي)
            $table->timestamps(); // تاريخ الإنشاء والتحديث
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
