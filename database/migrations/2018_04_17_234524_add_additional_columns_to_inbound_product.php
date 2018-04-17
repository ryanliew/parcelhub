<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalColumnsToInboundProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inbound_product', function (Blueprint $table) {
            $table->date('expiry_date')->nullable();
            $table->integer('quantity_received')->default(0);
            $table->string('remark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inbound_product', function (Blueprint $table) {
            $table->dropColumn(['expiry_date', 'quantity_received', 'remark']);
        });
    }
}
