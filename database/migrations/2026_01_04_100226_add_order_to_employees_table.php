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
        Schema::table('employees', function (Blueprint $table) {
            $table->integer('order')->default(0)->after('id');
        });
        
        // تعيين order للموظفين الموجودين
        $employees = \App\Models\Employee::all();
        foreach ($employees as $index => $employee) {
            $employee->order = $index + 1;
            $employee->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
