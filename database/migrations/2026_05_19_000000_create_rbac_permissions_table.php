<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRbacPermissionsTable extends Migration
{
    public function up()
    {
        Schema::create('rbac_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('permission');
            $table->string('role');
            $table->boolean('allowed')->default(false);
            $table->timestamps();

            $table->unique(['permission', 'role']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('rbac_permissions');
    }
}
