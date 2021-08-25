<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentGatewayDetailsToPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedInteger("payment_gateway_definition_id")->nullable();
            $table->string("payment_type");
            $table->text("payment_response")->nullable();
            $table->string("payment_status")->nullable();
            $table->string("payment_gateway_reference_id")->nullable();
            $table->string("payment_gateway_billplz_billid")->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn("payment_gateway_definition_id");
            $table->dropColumn("payment_type");
            $table->dropColumn("payment_response");
            $table->dropColumn("payment_status");
            $table->dropColumn("payment_gateway_reference_id");
            $table->dropColumn("payment_gateway_billplz_billid");
        });
    }
}
