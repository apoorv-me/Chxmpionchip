<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletManagementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_management', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bet_request_id');
            $table->string('game');
            $table->string('bet_between');
            $table->string('bet_amount');
            $table->string('service_charge');
            $table->string('total_chips');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wallet_management');
    }
}
