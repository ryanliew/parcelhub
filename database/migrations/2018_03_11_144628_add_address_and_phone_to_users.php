<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddressAndPhoneToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->string('address_2')->nullable();
            $table->string('phone')->nullable();
            $table->string('state')->nullable();
            $table->string('postcode')->nullable();
            $table->string('country')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['address', 'address_2', 'phone', 'state', 'postcode', 'country'])->change();
        });
    }
}
