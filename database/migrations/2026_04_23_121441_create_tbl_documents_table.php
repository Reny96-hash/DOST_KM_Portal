<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Note: This table already exists in the database.
     * This file is for version control only.
     */
    public function up(): void
    {
        // Table already exists - no action needed
        if (!Schema::hasTable('tbl_documents')) {
            Schema::create('tbl_documents', function (Blueprint $table) {
                $table->id('doc_id');
                $table->unsignedBigInteger('user_id');
                $table->string('doc_title', 500);
                $table->text('doc_description')->nullable();
                $table->string('doc_category', 100);
                $table->string('doc_file_path', 500);
                $table->string('doc_file_name', 255);
                $table->string('doc_file_type', 50);
                $table->bigInteger('doc_file_size')->nullable();
                $table->decimal('doc_version', 5, 1)->default(1.0);
                $table->string('security_clearance', 20)->default('Internal');
                $table->string('doc_status', 20)->default('published');
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->boolean('is_tacit_knowledge')->default(false);
                $table->string('expert_name', 255)->nullable();
                $table->date('expert_retirement_date')->nullable();
                $table->text('expert_methodology')->nullable();
                $table->integer('view_count')->default(0);
                $table->integer('download_count')->default(0);
                $table->timestamps();

                $table->foreign('user_id')->references('user_id')->on('tbl_users')->onDelete('cascade');
                $table->index('doc_category');
                $table->index('doc_title');
                $table->index('security_clearance');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Do not drop the table - it contains data
        // Schema::dropIfExists('tbl_documents');
    }
};
