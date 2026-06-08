<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('company_user', function (Blueprint $table) {
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_owner')->default(false);
            $table->timestamps();

            $table->primary(['company_id', 'user_id']);
        });

        Schema::create('company_dashboard_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('normal_color', 20)->default('#22c55e');
            $table->string('warning_color', 20)->default('#f59e0b');
            $table->string('critical_color', 20)->default('#ef4444');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_dashboard_settings');
        Schema::dropIfExists('company_user');
        Schema::dropIfExists('companies');
    }
};
