<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Recharge extends Model
{
    protected $table = 'users_recharge_history';

    protected $fillable = [
        'amount'
    ];

    function user()
    {
        return $this->belongsTo('App\Models\User\User')->select(['email', 'name']);
    }

    function vendor()
    {
        return $this->belongsTo('App\Models\User\User')->select(['email', 'name']);
    }
}
