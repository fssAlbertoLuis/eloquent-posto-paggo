<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class Purchase extends Model
{
    protected $table = 'users_purchase_history';

    protected $fillable = [
        'amount', 'company_id',
    ];

    function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    function user()
    {
        return $this->belongsTo('App\Models\User\User');
    }

    public function getFuelPriceAttribute($value)
    {
        return number_format($value, 3);
    }
    function getCostPriceAttribute($value)
    {
        return number_format($value, 3);
    }
}
