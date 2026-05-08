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
        Schema::create('attendances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('date');
            $table->uuid('santri_id');
            $table->boolean('status')->default(true); // true = hadir, false = absen
            $table->timestamps();

            $table->foreign('santri_id')
                ->references('id')
                ->on('santris')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unique(['date', 'santri_id']); // Satu santri hanya bisa satu record per tanggal
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
