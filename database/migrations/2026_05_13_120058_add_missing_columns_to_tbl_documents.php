<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tbl_documents', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('tbl_documents', 'content_type')) {
                $table->enum('content_type', ['file', 'article', 'link'])->default('file')->after('security_clearance');
            }
            if (!Schema::hasColumn('tbl_documents', 'is_question')) {
                $table->boolean('is_question')->default(false)->after('content_type');
            }
            if (!Schema::hasColumn('tbl_documents', 'parent_doc_id')) {
                $table->unsignedInteger('parent_doc_id')->nullable()->after('is_question');
                $table->foreign('parent_doc_id')->references('doc_id')->on('tbl_documents')->onDelete('cascade');
            }
            if (!Schema::hasColumn('tbl_documents', 'content_rich')) {
                $table->longText('content_rich')->nullable()->after('parent_doc_id');
            }
            if (!Schema::hasColumn('tbl_documents', 'allow_comments')) {
                $table->boolean('allow_comments')->default(true)->after('content_rich');
            }
            if (!Schema::hasColumn('tbl_documents', 'likes_count')) {
                $table->integer('likes_count')->default(0)->after('allow_comments');
            }
        });
    }

    public function down()
    {
        Schema::table('tbl_documents', function (Blueprint $table) {
            // Drop foreign key first
            if (Schema::hasColumn('tbl_documents', 'parent_doc_id')) {
                $table->dropForeign(['parent_doc_id']);
            }
            $table->dropColumn([
                'content_type', 'is_question', 'parent_doc_id',
                'content_rich', 'allow_comments', 'likes_count'
            ]);
        });
    }
};
