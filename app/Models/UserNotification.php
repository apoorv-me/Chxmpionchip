<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;

    protected $table = 'user_notification';
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public const SIGNUP = 1 , FRIDAY = 2, BET  = 3 , BET_RESULT = 4, FRIEND = 5, PROMO_CODE = 6;

    protected $fillable = [
        'user_id','type','chips','title','image','bet_request_id','friend_request_id','notification_key','created_at',
    ];

    protected $hidden = [
         'updated_at',
    ];
}
