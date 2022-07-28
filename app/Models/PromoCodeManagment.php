<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCodeManagment extends Model
{
    use HasFactory;

    protected $table = 'promo_code_managment';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'promo_code','promo_code_id',
    ];
}
