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
        Schema::table('subscriptions', function (Blueprint $table) {
            // إضافة الرابط بعد اسم الموقع
            $table->string('site_url')->nullable()->after('site_name');
            
            // تغيير username و password ليكونوا nullable
            $table->string('username')->nullable()->change();
            $table->string('password')->nullable()->change();
            
            // إضافة نوع تسجيل الدخول (user_pass أو gmail)
            $table->enum('login_type', ['user_pass', 'gmail'])->default('user_pass')->after('password');
            
            // إضافة نوع الاشتراك (رسمي أو مع شخص آخر)
            $table->enum('subscription_ownership', ['official', 'shared'])->default('official')->after('responsible_phone');
            
            // إضافة رقم الشخص الآخر (إذا كان shared)
            $table->string('shared_with_phone')->nullable()->after('subscription_ownership');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['site_url', 'login_type', 'subscription_ownership', 'shared_with_phone']);
            $table->string('username')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};
