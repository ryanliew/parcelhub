<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProformaColumnsIntoOutboundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outbounds', function (Blueprint $table) {
            $table->string('goods_description')->nullable();
            $table->string('payer_gst_vat')->nullable();
            $table->string('harm_comm_code')->nullable();
            $table->string('payment_term')->nullable();
            $table->string('trade_term')->nullable();
            $table->string('export_reason')->nullable();
            $table->boolean('is_business')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('outbounds', function (Blueprint $table) {
            $table->dropColumn('goods_description');
            $table->dropColumn('payer_gst_vat');
            $table->dropColumn('harm_comm_code');
            $table->dropColumn('payment_term');
            $table->dropColumn('trade_term');
            $table->dropColumn('export_reason');
            $table->dropColumn('is_business');
        });
    }
}
