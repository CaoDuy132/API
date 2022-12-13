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
        Schema::create('cate_posts', function (Blueprint $table) {
            $table->integer('id',true,false);
            $table->text('nameCatePosts');
            $table->text('slugCatePost');
            $table->text('logo')->nullable();
            $table->text('der')->nullable();
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
        Schema::dropIfExists('cate_posts');
    }
};
