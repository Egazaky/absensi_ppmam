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
        Schema::table('attendances', function (Blueprint $table) {
            // Drop unique constraint lama
            $table->dropUnique(['date', 'santri_id']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            // Tambahkan kolom session
            $table->enum('session', ['Subuh', 'Isya'])->default('Subuh')->after('santri_id');
        });

        Schema::table('attendances', function (Blueprint $table) {
            // Tambahkan unique constraint baru dengan session
            $table->unique(['date', 'santri_id', 'session']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Drop unique constraint baru
            $table->dropUnique(['date', 'santri_id', 'session']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            // Hapus kolom session
            $table->dropColumn('session');
        });

        Schema::table('attendances', function (Blueprint $table) {
            // Kembalikan unique constraint lama
            $table->unique(['date', 'santri_id']);
        });
    }
};
