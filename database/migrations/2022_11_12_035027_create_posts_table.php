<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->integer('id',true,false);
            $table->integer('idcatePosts');
            $table->text('titlePosts');
            $table->text('slugPosts');
            $table->text('summaryPosts');
            $table->foreign('idcatePosts')->references('id')->on('cate_posts');
            $table->text('imagePosts');
            $table->text('tagsPosts');
            $table->text('contentPosts');
            $table->text('author');
            $table->integer('viewPosts');
            $table->boolean('statusPosts')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
};
