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
    Schema::create('characters', function (Blueprint $table) {
        $table->id();

    $table->foreignId('campaign_id')
        ->nullable()
        ->constrained()
        ->restrictOnDelete();

        $table->string('name')->nullable();
        $table->unsignedSmallInteger('age')->nullable();
        $table->text('backstory')->nullable();

        $table->string('creation_status')->default('draft');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
