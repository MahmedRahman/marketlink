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
        // التحقق من وجود العمود قبل حذفه
        if (Schema::hasColumn('employees', 'roles')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('roles');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->json('roles')->nullable();
        });
    }
};
