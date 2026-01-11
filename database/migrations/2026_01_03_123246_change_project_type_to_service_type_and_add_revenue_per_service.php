<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // إسقاط الحقل القديم وإضافة الحقل الجديد
            $table->dropColumn('monthly_revenue');
        });
        
        // تغيير اسم الحقل من project_type إلى service_type
        DB::statement('ALTER TABLE projects RENAME COLUMN project_type TO service_type');
        
        Schema::table('projects', function (Blueprint $table) {
            // إضافة service_revenue كـ JSON
            $table->json('service_revenue')->nullable()->after('service_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('service_revenue');
        });
        
        // إعادة تغيير اسم الحقل
        DB::statement('ALTER TABLE projects RENAME COLUMN service_type TO project_type');
        
        Schema::table('projects', function (Blueprint $table) {
            $table->decimal('monthly_revenue', 10, 2)->nullable();
        });
    }
};
