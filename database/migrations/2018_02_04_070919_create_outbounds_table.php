<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutboundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outbounds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('courier_id');
            $table->integer('product');
            $table->string('recipient_name');
            $table->string('recipient_address');
            $table->boolean('insurance');
            $table->float('amount_insured');
            $table->boolean('dangerous');
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
        Schema::dropIfExists('outbounds');
    }
}
