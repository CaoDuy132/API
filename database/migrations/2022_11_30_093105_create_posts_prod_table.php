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
        Schema::create('posts_prod', function (Blueprint $table) {
            $table->id();
            $table->integer('id_posts');
            $table->integer('id_prod');
            $table->foreign('id_posts')->references('id')->on('posts');
            $table->foreign('id_prod')->references('id')->on('products');
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
        Schema::dropIfExists('posts_prod');
    }
};
