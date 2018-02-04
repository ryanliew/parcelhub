<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInboundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inbounds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('product');
            $table->integer('quantity');
            $table->dateTime('arrival_date');
            $table->integer('total_carton');
            $table->string('status');
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
        Schema::dropIfExists('inbounds');
    }
}
