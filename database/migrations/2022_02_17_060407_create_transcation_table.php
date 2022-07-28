<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranscationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transcation', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable();
            $table->string('transactionstype')->nullable();
            $table->string('transactionsname')->nullable();
            $table->string('title')->nullable();
            $table->string('transactionsamount')->nullable();
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
        Schema::dropIfExists('transcation');
    }
}
