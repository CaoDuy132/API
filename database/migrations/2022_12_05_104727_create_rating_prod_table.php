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
        Schema::create('rating_prod', function (Blueprint $table) {
            $table->id();
            $table->integer('idProd');
            $table->integer('idCustomer');
            $table->foreign('idProd')->references('id')->on('products');
            $table->foreign('idCustomer')->references('id')->on('customers');
            $table->integer('num_star')->default(0);
            $table->text('content_review');
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
        Schema::dropIfExists('rating_prod');
    }
};
