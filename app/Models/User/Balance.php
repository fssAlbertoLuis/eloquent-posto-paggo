<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    //
    function user()
    {
        return $this->belongsTo('App\Models\User\User');
    }
}
