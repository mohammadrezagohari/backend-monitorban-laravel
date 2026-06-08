<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_resource_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->morphs('resource');
            $table->timestamps();

            $table->unique(['group_id', 'resource_id', 'resource_type'], 'group_resource_access_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_resource_access');
    }
};
