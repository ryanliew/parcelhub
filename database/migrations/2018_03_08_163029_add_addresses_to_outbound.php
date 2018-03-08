<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddressesToOutbound extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outbounds', function (Blueprint $table) {
            $table->string('recipient_address_2')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->string('recipient_state')->nullable();
            $table->string('recipient_postcode')->nullable();
            $table->string('recipient_country')->nullable();
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
            $table->dropColumn(['recipient_address_2', 'recipient_phone', 'recipient_state', 'recipient_postcode', 'recipient_country'])->change();
        });
    }
}
