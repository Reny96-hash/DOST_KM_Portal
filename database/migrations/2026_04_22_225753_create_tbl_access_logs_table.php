<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_access_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('doc_id')->nullable();
            $table->string('action_type', 50);
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->text('details')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('tbl_users')->onDelete('cascade');
            $table->index('action_type');
            $table->index('created_at');
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_access_logs');
    }
};
