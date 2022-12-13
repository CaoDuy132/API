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
        Schema::table('billdetail', function(Blueprint $table){
            $table->integer('id')->primary();
            $table->integer('idBill');
            $table->integer('idStorage');
            $table->integer('quantity',2);
            $table->timestamps();
            $table->foreign('idBill')->references('id')->on('bills');
            $table->foreign('idProduct')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('billdetail');
    }
};
