<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('important_accounts', function (Blueprint $table) {
            $table->string('month', 7)->nullable()->after('notes')->comment('Y-m');
        });

        foreach (DB::table('important_accounts')->get() as $row) {
            $month = $row->created_at ? date('Y-m', strtotime($row->created_at)) : date('Y-m');
            DB::table('important_accounts')->where('id', $row->id)->update(['month' => $month]);
        }
    }

    public function down(): void
    {
        Schema::table('important_accounts', function (Blueprint $table) {
            $table->dropColumn('month');
        });
    }
};
