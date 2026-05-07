<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tbl_documents', function (Blueprint $table) {
            // Only add columns if they don't exist
            if (!Schema::hasColumn('tbl_documents', 'approval_status')) {
                $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
            }

            if (!Schema::hasColumn('tbl_documents', 'reviewed_by')) {
                $table->unsignedBigInteger('reviewed_by')->nullable();
            }

            if (!Schema::hasColumn('tbl_documents', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable();
            }

            if (!Schema::hasColumn('tbl_documents', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('tbl_documents', function (Blueprint $table) {
            $table->dropColumn(['approval_status', 'reviewed_by', 'reviewed_at', 'rejection_reason']);
        });
    }
};
