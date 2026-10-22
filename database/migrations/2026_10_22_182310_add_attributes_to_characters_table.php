<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->unsignedTinyInteger('agi')->default(1);
            $table->unsignedTinyInteger('forca')->default(1);
            $table->unsignedTinyInteger('intelecto')->default(1);
            $table->unsignedTinyInteger('presenca')->default(1);
            $table->unsignedTinyInteger('vigor')->default(1);
        });
    }

    public function down(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->dropColumn([
                'agi',
                'forca',
                'intelecto',
                'presenca',
                'vigor',
            ]);
        });
    }
};