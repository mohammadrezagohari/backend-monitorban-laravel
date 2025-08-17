<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('group_has_permissions', function (Blueprint $table) {
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->primary(['group_id', 'permission_id']);
        });

        Schema::create('model_has_groups', function (Blueprint $table) {
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->morphs('model'); // model_type + model_id
            $table->primary(['group_id', 'model_id', 'model_type'], 'model_has_groups_primary');
            $table->index(['model_id', 'model_type'], 'model_has_groups_model_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_has_groups');
        Schema::dropIfExists('group_has_permissions');
        Schema::dropIfExists('groups');
    }
};
