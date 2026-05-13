// database/migrations/2025_01_01_000003_create_tbl_document_attachments_table.php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_document_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('doc_id');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type', 50);
            $table->integer('file_size');
            $table->timestamps();

            $table->foreign('doc_id')->references('doc_id')->on('tbl_documents')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_document_attachments');
    }
};
