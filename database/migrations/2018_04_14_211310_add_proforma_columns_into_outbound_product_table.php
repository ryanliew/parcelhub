<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProformaColumnsIntoOutboundProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outbound_product', function (Blueprint $table) {
            $table->string('unit_value')->nullable();
            $table->string('total_value')->nullable();
            $table->string('weight')->nullable();
            $table->string('manufacture_country')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('outbound_product', function (Blueprint $table) {
            $table->dropColumn('unit_value');
            $table->dropColumn('total_value');
            $table->dropColumn('weight');
            $table->dropColumn('manufacture_country');
        });
    }
}
