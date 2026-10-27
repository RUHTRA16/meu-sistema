<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_automation_settings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('campaign_id')
                ->constrained('campaigns')
                ->cascadeOnDelete()
                ->unique();

            $table->string('dice_rolling', 20)
                ->default('manual');

            $table->string('initiative', 20)
                ->default('manual');

            $table->string('npc_control', 20)
                ->default('manual');

            $table->string('ai_actions', 20)
                ->default('confirm');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_automation_settings');
    }
};