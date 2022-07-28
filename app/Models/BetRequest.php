<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BetRequest extends Model
{
    use HasFactory;

    protected $table = 'bet_request';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'GameID','first_user','second_user','first_user_team','second_user_team','chips','action','first_user_sc','second_user_sc',
    ];
}
