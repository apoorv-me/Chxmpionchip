<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsReadToUserNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_notification', function (Blueprint $table) {
            $table->enum('is_read', [1,0])->default(0)->after('notification_key')->nullable();
            // 1== Read
            // 0 == Unread
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_notification', function (Blueprint $table) {
            //
        });
    }
}
