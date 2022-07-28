<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Games extends Model
{
    use HasFactory;

    protected $table = 'games';
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'GameID','League','Status','Season','Awayteam','Hometeam','DatetimeUTC','AwayTeamScore','HomeTeamScore',
    ];
    
}
