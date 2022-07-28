<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserWallets extends Model
{
    use HasFactory;

    protected $table = 'user_wallets';
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','available_chips'
    ];

    protected $hidden = [
        'id','user_id', 'created_at','updated_at',
    ];

}
