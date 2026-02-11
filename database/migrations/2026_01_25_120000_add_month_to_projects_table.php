<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('month', 7)->nullable()->after('status')->comment('Y-m');
        });

        // تعبئة الشهر من تاريخ الإنشاء للمشاريع الحالية
        $projects = DB::table('projects')->get();
        foreach ($projects as $row) {
            $month = $row->created_at ? date('Y-m', strtotime($row->created_at)) : date('Y-m');
            DB::table('projects')->where('id', $row->id)->update(['month' => $month]);
        }
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('month');
        });
    }
};
