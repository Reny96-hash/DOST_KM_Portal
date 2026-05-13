// database/migrations/2025_01_01_000005_create_tbl_likes_table.php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('doc_id')->nullable();
            $table->unsignedBigInteger('comment_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('tbl_users');
            $table->foreign('doc_id')->references('doc_id')->on('tbl_documents')->onDelete('cascade');
            $table->foreign('comment_id')->references('comment_id')->on('tbl_comments')->onDelete('cascade');
            $table->unique(['user_id', 'doc_id', 'comment_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_likes');
    }
};
