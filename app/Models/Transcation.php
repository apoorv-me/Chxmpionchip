<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transcation extends Model
{
    use HasFactory;

    protected $table = 'transcation';
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bet_id','user_id','transactionstype','transactionsname','title','transactionsamount','image',
    ];

    protected $hidden = [
        'user_id', 'created_at','updated_at',
    ];
}
