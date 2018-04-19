<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOriginalQuantityToInboundLot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inbound_product_lot', function (Blueprint $table) {
            $table->integer('quantity_original')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inbound_product_lot', function (Blueprint $table) {
            $table->dropColumn(['quantity_original']);
        });
    }
}
