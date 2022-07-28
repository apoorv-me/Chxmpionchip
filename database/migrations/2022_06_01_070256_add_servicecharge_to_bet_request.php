<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServicechargeToBetRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bet_request', function (Blueprint $table) {
            $table->string('first_user_sc')->nullable()->after('second_user_team');
            $table->string('second_user_sc')->nullable()->after('first_user_sc');
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
