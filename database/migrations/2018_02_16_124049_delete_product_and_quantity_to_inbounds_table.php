<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteProductAndQuantityToInboundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inbounds', function (Blueprint $table) {
            $table->dropColumn('product')->change();
            $table->dropColumn('quantity')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inbounds', function (Blueprint $table) {
            $table->integer('product')->change();
            $table->integer('quantity')->change();
        });
    }
}
