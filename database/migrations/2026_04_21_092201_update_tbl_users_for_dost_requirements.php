<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tbl_users', function (Blueprint $table) {
            $table->string('user_division', 200)->nullable()->after('user_last_name');


            $table->dropColumn('user_role');
        });

        Schema::table('tbl_users', function (Blueprint $table) {
            $table->enum('user_role', ['Admin', 'Regular User'])->default('Regular User')->after('user_division');
        });
    }

    public function down()
    {
        Schema::table('tbl_users', function (Blueprint $table) {
            $table->dropColumn('user_division');
            $table->dropColumn('user_role');
        });

        Schema::table('tbl_users', function (Blueprint $table) {
            $table->enum('user_role', ['staff', 'info_owner', 'km_champion', 'edts_admin', 'director'])->default('staff')->after('user_last_name');
        });
    }
};
