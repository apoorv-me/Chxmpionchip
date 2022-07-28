<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BetList extends Model
{
    use HasFactory;

    protected $table = 'bet_list';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'GameID','result','bet_id',
    ];
}
