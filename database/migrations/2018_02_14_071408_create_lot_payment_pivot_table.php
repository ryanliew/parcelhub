<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLotPaymentPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lot_payment', function (Blueprint $table) {
            $table->integer('lot_id')->unsigned();
            $table->integer('payment_id')->unsigned();

            $table->foreign('lot_id')->references('id')->on('lots');
            $table->foreign('payment_id')->references('id')->on('payments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lot_payment');
    }
}
