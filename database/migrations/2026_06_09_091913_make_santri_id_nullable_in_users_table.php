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
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['santri_id']);
            $table->uuid('santri_id')->nullable()->change();
            $table->foreign('santri_id')
                ->references('id')
                ->on('santris')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['santri_id']);
            $table->uuid('santri_id')->nullable(false)->change();
            $table->foreign('santri_id')
                ->references('id')
                ->on('santris')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }
};
