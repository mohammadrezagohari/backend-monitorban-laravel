<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sensor_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key')->unique();
            $table->string('value_type')->default('number');
            $table->timestamps();
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('symbol');
            $table->string('dimension')->nullable();
            $table->boolean('is_canonical')->default(false);
            $table->timestamps();
        });

        Schema::table('server_rooms', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('name')->nullable()->after('company_id');
            $table->string('location')->nullable()->after('name');
            $table->text('description')->nullable()->after('location');
        });

        Schema::table('sensors', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('sensor_type_id')->nullable()->after('server_room_id')->constrained()->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->after('sensor_type_id')->constrained('units')->nullOnDelete();
            $table->string('code')->nullable()->after('name');
            $table->boolean('is_active')->default(true)->after('profile_picture');
        });

        Schema::create('sensor_threshold_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sensor_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->string('name');
            $table->decimal('normal_min', 12, 4)->nullable();
            $table->decimal('normal_max', 12, 4)->nullable();
            $table->decimal('warning_min', 12, 4)->nullable();
            $table->decimal('warning_max', 12, 4)->nullable();
            $table->decimal('critical_min', 12, 4)->nullable();
            $table->decimal('critical_max', 12, 4)->nullable();
            $table->timestamps();
        });

        Schema::create('sensor_thresholds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sensor_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->decimal('normal_min', 12, 4)->nullable();
            $table->decimal('normal_max', 12, 4)->nullable();
            $table->decimal('warning_min', 12, 4)->nullable();
            $table->decimal('warning_max', 12, 4)->nullable();
            $table->decimal('critical_min', 12, 4)->nullable();
            $table->decimal('critical_max', 12, 4)->nullable();
            $table->timestamps();
        });

        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sensor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->decimal('value_numeric', 16, 4)->nullable();
            $table->string('value_text')->nullable();
            $table->boolean('value_boolean')->nullable();
            $table->timestamp('recorded_at')->index();
            $table->timestamps();

            $table->index(['sensor_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensor_readings');
        Schema::dropIfExists('sensor_thresholds');
        Schema::dropIfExists('sensor_threshold_profiles');

        Schema::table('sensors', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
            $table->dropConstrainedForeignId('sensor_type_id');
            $table->dropConstrainedForeignId('unit_id');
            $table->dropColumn(['code', 'is_active']);
        });

        Schema::table('server_rooms', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
            $table->dropColumn(['name', 'location', 'description']);
        });

        Schema::dropIfExists('units');
        Schema::dropIfExists('sensor_types');
    }
};
