<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_categories', function (Blueprint $table) {
            $table->id('cat_id');
            $table->string('cat_name', 100)->unique();
            $table->string('cat_description', 255)->nullable();
            $table->timestamps();
        });

        // Insert default categories
        DB::table('tbl_categories')->insert([
            ['cat_name' => 'Research Papers', 'created_at' => now(), 'updated_at' => now()],
            ['cat_name' => 'Policies', 'created_at' => now(), 'updated_at' => now()],
            ['cat_name' => 'Project Reports', 'created_at' => now(), 'updated_at' => now()],
            ['cat_name' => 'Technical Guides', 'created_at' => now(), 'updated_at' => now()],
            ['cat_name' => 'Administrative', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('tbl_categories');
    }
};
