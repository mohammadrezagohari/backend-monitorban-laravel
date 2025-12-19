<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sensors', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // نام سنسور
            $table->unsignedBigInteger('server_room_id'); // اتاق سرور
            $table->string('type'); // نوع سنسور
            $table->string('title_fa'); // عنوان فارسی سنسور
            $table->string('title_en'); // عنوان انگلیسی سنسور
            $table->string('alert_type'); // نوع اعلان
            $table->string('physical_address')->nullable(); // آدرس فیزیکی سنسور
            $table->string('unit')->nullable(); // واحد اندازه‌گیری
            $table->integer('alert_interval')->nullable(); // فواصل بین اعلان
            $table->integer('alert_count')->nullable(); // تعداد اعلان
            $table->integer('min_daily_record')->nullable(); // حداقل ثبت روزانه
            $table->string('recordable_changes')->nullable(); // تغییرات قابل ثبت
            $table->boolean('has_critical_history')->default(false); // پیشینه بحرانی
            $table->boolean('has_warning_history')->default(false); // پیشینه اخطار
            $table->boolean('crisis_committee')->default(false); // کمیته بحران
            $table->string('icon')->nullable(); // آیکون
            $table->string('profile_picture')->nullable(); // عکس پروفایل
            $table->timestamps();

            $table->foreign('server_room_id')->references('id')->on('server_rooms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensors');
    }
};
