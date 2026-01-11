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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // الاسم
            $table->string('phone'); // رقم التليفون
            $table->json('role')->nullable(); // الدور (يمكن اختيار أكثر من دور)
            $table->string('status')->default('active'); // الحالة
            $table->decimal('monthly_salary', 10, 2)->nullable(); // المرتب الشهري
            $table->timestamps(); // تاريخ الإنشاء والتحديث
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
