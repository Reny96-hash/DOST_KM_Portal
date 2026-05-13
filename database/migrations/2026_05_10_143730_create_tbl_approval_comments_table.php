// database/migrations/2025_01_01_000007_create_tbl_approval_comments_table.php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_approval_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('doc_id');
            $table->unsignedInteger('admin_id');
            $table->text('comment');
            $table->timestamps();

            $table->foreign('doc_id')->references('doc_id')->on('tbl_documents')->onDelete('cascade');
            $table->foreign('admin_id')->references('user_id')->on('tbl_users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_approval_comments');
    }
};
