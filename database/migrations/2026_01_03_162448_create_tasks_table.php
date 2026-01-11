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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('title'); // عنوان المهمة
            $table->text('description')->nullable(); // وصف المهمة
            $table->enum('status', ['todo', 'in_progress', 'done'])->default('todo'); // حالة المهمة
            $table->integer('priority')->default(1); // الأولوية (1-5)
            $table->date('due_date')->nullable(); // تاريخ الاستحقاق
            $table->string('month'); // الشهر (Y-m format)
            $table->integer('order')->default(0); // ترتيب المهمة في العمود
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
