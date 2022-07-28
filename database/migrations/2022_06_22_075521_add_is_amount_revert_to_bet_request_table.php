<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsAmountRevertToBetRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bet_request', function (Blueprint $table) {
            $table->enum('isAmountRevert', [1,0])->default(0)->after('notifiy');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bet_request', function (Blueprint $table) {
            //
        });
    }
}
