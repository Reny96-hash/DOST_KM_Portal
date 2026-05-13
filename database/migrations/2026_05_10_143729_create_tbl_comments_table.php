// database/migrations/2025_01_01_000004_create_tbl_comments_table.php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_comments', function (Blueprint $table) {
            $table->id('comment_id');
            $table->unsignedInteger('doc_id');
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('parent_comment_id')->nullable();
            $table->text('comment_text');
            $table->enum('comment_type', ['comment', 'answer'])->default('comment');
            $table->integer('likes')->default(0);
            $table->timestamps();

            $table->foreign('doc_id')->references('doc_id')->on('tbl_documents')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('tbl_users');
            $table->foreign('parent_comment_id')->references('comment_id')->on('tbl_comments')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_comments');
    }
};
