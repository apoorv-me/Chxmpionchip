<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletManagement extends Model
{
    use HasFactory;

    protected $table = 'wallet_management';
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bet_request_id','game','bet_between','bet_amount','service_charge','total_chips',
    ];

    public const REGISTER = 'Registration bonus' , REFER = 'Referral Bonus', 
        REGISTER_WITH_REFERRAL = 'with referral' , FRIDAY_DEPOSIT = 'Friday Bonus',
        BET_WIN = 'WIN', BET_LOST = 'LOST';

    public const REGISTER_CHIPS = 50, REFER_CHIPS = 10, 
                 REGISTER_WITH_REFERRAL_CHIPS = 60, FRIDAY_DEPOSIT_CHIPS = 20;

}
