<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdminRemarksToInboundProductLot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inbound_product_lot', function (Blueprint $table) {
            $table->string('remark')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('quantity_received')->default(0);
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
            $table->dropColumn(['remark', 'expiry_date', 'quantity_received']);
        });
    }
}
