<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_sessions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('campaign_id')
                ->constrained('campaigns')
                ->cascadeOnDelete();

            $table->unsignedInteger('session_number');
            $table->string('title', 150);
            $table->date('scheduled_at')->nullable();
            $table->text('summary')->nullable();

            $table->string('status', 20)->default('planned');

            $table->timestamps();

            $table->unique(['campaign_id', 'session_number']);
            $table->index(['campaign_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_sessions');
    }
};