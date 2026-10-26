<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona os recursos dos personagens.
     */
    public function up(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->unsignedSmallInteger('pv_max')->nullable();
            $table->unsignedSmallInteger('pv_current')->nullable();

            $table->unsignedSmallInteger('san_max')->nullable();
            $table->unsignedSmallInteger('san_current')->nullable();

            $table->unsignedSmallInteger('pe_max')->nullable();
            $table->unsignedSmallInteger('pe_current')->nullable();
        });
    }

    /**
     * Remove os recursos dos personagens, se necessário.
     */
    public function down(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->dropColumn([
                'pv_max',
                'pv_current',
                'san_max',
                'san_current',
                'pe_max',
                'pe_current',
            ]);
        });
    }
};
