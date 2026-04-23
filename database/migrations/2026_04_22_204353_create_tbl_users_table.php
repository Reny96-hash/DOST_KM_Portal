<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_users', function (Blueprint $table) {
            $table->id('user_id');

            // Employee information
            $table->string('emp_id', 20)->unique();
            $table->string('user_first_name', 100);
            $table->string('user_last_name', 100);
            $table->string('user_middle_initial', 5)->nullable();
            $table->string('user_division', 200)->nullable();
            $table->string('user_designation', 100)->nullable();

            // Authentication
            $table->string('user_email', 255)->unique();
            $table->string('user_password_hash', 255);
            $table->string('user_password_temp', 255)->nullable();
            $table->timestamp('user_password_temp_expires')->nullable();
            $table->boolean('user_must_change_password')->default(true);

            // Security
            $table->string('security_clearance', 20)->default('Internal');
            $table->string('user_role', 20)->default('staff');
            $table->string('user_status', 20)->default('active');

            // Security features
            $table->integer('login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();

            // Audit
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
            $table->softDeletes('user_deleted_at');

            // Indexes
            $table->index('user_role');
            $table->index('user_status');
            $table->index('user_division');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_users');
    }
};
