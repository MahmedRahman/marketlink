<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('important_account_employee', function (Blueprint $table) {
            $table->id();
            $table->foreignId('important_account_id')->constrained('important_accounts')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['important_account_id', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('important_account_employee');
    }
};
