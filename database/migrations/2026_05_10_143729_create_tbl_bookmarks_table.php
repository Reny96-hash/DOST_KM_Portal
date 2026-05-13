// database/migrations/2025_01_01_000006_create_tbl_bookmarks_table.php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_bookmarks', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('doc_id');
            $table->timestamps();

            $table->primary(['user_id', 'doc_id']);
            $table->foreign('user_id')->references('user_id')->on('tbl_users');
            $table->foreign('doc_id')->references('doc_id')->on('tbl_documents')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_bookmarks');
    }
};
