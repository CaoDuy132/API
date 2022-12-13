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
        Schema::table('bills', function(Blueprint $table){
            $table->integer('id')->primary()->autoIncrement();
            $table->integer('idCustiomer');
            $table->string('recieverName',255);
            $table->string('recieverPhone',10);
            $table->string('note',255);
            $table->timestamps();
            $table->foreign('idCustiomer')->references('id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bills');
    }
};
