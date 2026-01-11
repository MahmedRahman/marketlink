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
        // في SQLite، نحتاج إلى إعادة إنشاء الجدول
        if (config('database.default') === 'sqlite') {
            // حذف foreign key constraints أولاً
            DB::statement('PRAGMA foreign_keys=OFF');
            
            // حذف الجدول المؤقت إذا كان موجوداً
            DB::statement('DROP TABLE IF EXISTS tasks_new');
            
            // إنشاء جدول مؤقت بنفس البنية لكن employee_id nullable
            DB::statement('CREATE TABLE tasks_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                employee_id INTEGER NULL,
                project_id INTEGER NULL,
                title TEXT NOT NULL,
                description TEXT NULL,
                status TEXT NOT NULL DEFAULT "todo",
                priority INTEGER NOT NULL DEFAULT 1,
                due_date TEXT NULL,
                month TEXT NOT NULL,
                "order" INTEGER NOT NULL DEFAULT 0,
                created_at TEXT NULL,
                updated_at TEXT NULL
            )');
            
            // نسخ البيانات مع معالجة القيم NULL
            DB::statement('INSERT INTO tasks_new (id, employee_id, project_id, title, description, status, priority, due_date, month, "order", created_at, updated_at)
                SELECT id, 
                       CASE WHEN employee_id IS NULL THEN NULL ELSE employee_id END,
                       project_id,
                       title,
                       description,
                       COALESCE(status, "todo"),
                       COALESCE(priority, 1),
                       due_date,
                       month,
                       COALESCE("order", 0),
                       created_at,
                       updated_at
                FROM tasks');
            
            // حذف الجدول القديم
            Schema::drop('tasks');
            
            // إعادة تسمية الجدول الجديد
            DB::statement('ALTER TABLE tasks_new RENAME TO tasks');
            
            // إعادة تفعيل foreign keys
            DB::statement('PRAGMA foreign_keys=ON');
        } else {
            // للقواعد الأخرى (MySQL, PostgreSQL)
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropForeign(['employee_id']);
            });
            
            Schema::table('tasks', function (Blueprint $table) {
                $table->foreignId('employee_id')->nullable()->change();
            });
            
            Schema::table('tasks', function (Blueprint $table) {
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (config('database.default') === 'sqlite') {
            // إنشاء جدول مؤقت مع employee_id مطلوب
            DB::statement('CREATE TABLE tasks_old (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                employee_id INTEGER NOT NULL,
                project_id INTEGER NULL,
                title TEXT NOT NULL,
                description TEXT NULL,
                status TEXT NOT NULL DEFAULT "todo",
                priority INTEGER NOT NULL DEFAULT 1,
                due_date DATE NULL,
                month TEXT NOT NULL,
                "order" INTEGER NOT NULL DEFAULT 0,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL
            )');
            
            // نسخ البيانات (فقط المهام التي لها employee_id)
            DB::statement('INSERT INTO tasks_old SELECT * FROM tasks WHERE employee_id IS NOT NULL');
            
            // حذف الجدول القديم
            Schema::drop('tasks');
            
            // إعادة تسمية الجدول الجديد
            DB::statement('ALTER TABLE tasks_old RENAME TO tasks');
        } else {
            Schema::table('tasks', function (Blueprint $table) {
                $table->foreignId('employee_id')->nullable(false)->change();
            });
        }
    }
};
