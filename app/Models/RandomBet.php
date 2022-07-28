<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RandomBet extends Model
{
    use HasFactory;

    protected $table = 'random_bet';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','GameID','League','Status','user_team','against_team','Chips','created_at','updated_at',
    ];

}
