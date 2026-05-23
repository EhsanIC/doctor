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
        Schema::create('doctor_profiles', function (Blueprint $table) {
            $table->id();

            // Foreign key به جدول users
            $table->foreignId('user_id')
                ->unique() // هر کاربر فقط یک پروفایل پزشک داشته باشه
                ->constrained() // این به طور خودکار foreign key رو تنظیم می‌کنه
                ->cascadeOnDelete(); // اگر کاربر حذف شد، پروفایلش هم حذف بشه

            // Foreign key به جدول specialties (اگر وجود دارد)
            $table->foreignId('specialty_id')
                ->nullable() // تخصص می‌تونه خالی باشه
                ->constrained()
                ->nullOnDelete(); // اگر تخصص حذف شد، اینجا null میشه

            // ستون های جدید
            $table->string('mobile', 20)->nullable()->unique(); // شماره موبایل (اختیاری و یکتا)
            $table->string('medical_code')->nullable()->unique(); // کد نظام پزشکی (اختیاری و یکتا)
            $table->text('address')->nullable(); // آدرس (اختیاری)
            $table->text('working_hours')->nullable(); // زمان کاری (اختیاری)

            // ستون های قبلی که تغییر نکردند
            $table->string('status')->default('pending'); // وضعیت پروفایل (پیش‌فرض pending)
            $table->string('image')->nullable(); // مسیر تصویر پروفایل (اختیاری)
            $table->text('bio')->nullable(); // توضیحات یا بیوگرافی (اختیاری)

            $table->timestamps(); // created_at و updated_at
            $table->softDeletes(); // اضافه کردن ستون deleted_at برای soft delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_profiles');
    }
};
