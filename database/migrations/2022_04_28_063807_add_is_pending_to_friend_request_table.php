<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsPendingToFriendRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('friend_request', function (Blueprint $table) {
            $table->bigInteger('is_request')->nullable()->after('action');
            $table->bigInteger('is_pending')->nullable()->after('is_request');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('friend_request', function (Blueprint $table) {
            //
        });
    }
}
