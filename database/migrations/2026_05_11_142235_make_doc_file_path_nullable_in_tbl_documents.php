<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tbl_documents', function (Blueprint $table) {
            $table->string('doc_file_path')->nullable()->change();
            $table->string('doc_file_name')->nullable()->change();
            $table->string('doc_file_type')->nullable()->change();
            $table->decimal('doc_file_size', 10, 2)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('tbl_documents', function (Blueprint $table) {
            $table->string('doc_file_path')->nullable(false)->change();
            $table->string('doc_file_name')->nullable(false)->change();
            $table->string('doc_file_type')->nullable(false)->change();
            $table->decimal('doc_file_size', 10, 2)->nullable(false)->change();
        });
    }
};
