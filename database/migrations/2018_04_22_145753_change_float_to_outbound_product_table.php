<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFloatToOutboundProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outbound_product', function (Blueprint $table) {
            $table->float('unit_value')->nullable()->change();
            $table->float('total_value')->nullable()->change();
            $table->float('weight')->nullable()->change();
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
            $table->string('unit_value')->change();
            $table->string('total_value')->change();
            $table->string('weight')->change();
        });
    }
}
