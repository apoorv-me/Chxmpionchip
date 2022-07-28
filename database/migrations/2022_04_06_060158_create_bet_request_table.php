<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBetRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bet_request', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('GameID');
            $table->bigInteger('chips');
            $table->bigInteger('first_user');
            $table->bigInteger('second_user');
            $table->string('first_user_team');
            $table->string('second_user_team');
            $table->enum('action', ['Accept','Decline','Cancel','Pending'])->default('Pending');
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
        Schema::dropIfExists('bet_request');
    }
}
