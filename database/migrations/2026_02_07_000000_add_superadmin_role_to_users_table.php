<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSuperadminRoleToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // For MySQL, we need to modify the enum column
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('SuperAdmin', 'Administrator', 'Pengurus', 'Santri')");
        } else {
            // For other databases, you might need different approach
            Schema::table('users', function (Blueprint $table) {
                // This is a fallback for non-MySQL databases
                // Adjust as needed for your specific database
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('Administrator', 'Pengurus', 'Santri')");
        }
    }
}
