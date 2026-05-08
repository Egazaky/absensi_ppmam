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
            // Modifikasi ENUM untuk menambahkan Qiyamullail
            $table->enum('session', ['Subuh', 'Isya', 'Qiyamullail'])->default('Subuh')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Revert ke enum lama tanpa Qiyamullail
            $table->enum('session', ['Subuh', 'Isya'])->default('Subuh')->change();
        });
    }
};
