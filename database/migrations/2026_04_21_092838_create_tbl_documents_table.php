<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_documents', function (Blueprint $table) {
            // Primary key
            $table->id('doc_id');

            $table->unsignedBigInteger('user_id');

            // Document metadata
            $table->string('doc_title', 500);
            $table->text('doc_description')->nullable();
            $table->string('doc_category', 100);

            // File information
            $table->string('doc_file_path', 500);
            $table->string('doc_file_name', 255);
            $table->string('doc_file_type', 50);
            $table->integer('doc_file_size')->nullable();

            // Version control (auto-incrementing)
            $table->decimal('doc_version', 5, 1)->default(1.0);

            // Document status
            $table->enum('doc_status', ['draft', 'pending_review', 'published', 'archived'])->default('draft');

            $table->enum('security_clearance', ['Public', 'Internal', 'Confidential', 'Secret', 'Top Secret'])->default('Internal');

            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->boolean('is_tacit_knowledge')->default(false);
            $table->string('expert_name', 255)->nullable();
            $table->date('expert_retirement_date')->nullable();
            $table->text('expert_methodology')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('user_id')->on('tbl_users')->onDelete('cascade');
            $table->foreign('approved_by')->references('user_id')->on('tbl_users')->onDelete('set null');

            // Indexes for search performance
            $table->index('doc_category');
            $table->index('doc_title');
            $table->index('security_clearance');
            $table->index('doc_status');
            $table->index('is_tacit_knowledge');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_documents');
    }
};
