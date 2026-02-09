<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Ticket\Enums\TicketStatusEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link to user
            $table->string('subject'); // e.g., "عیب یابی سنسور"
            $table->string('recipient'); // e.g., Department name
            $table->text('message');
            $table->string('status')->default(TicketStatusEnum::OPEN->value); // open, closed, pending
            $table->unsignedBigInteger('last_change_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
