<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'cnpj'];

    function owner()
    {
        return $this->hasOne('App\Models\User\User');
    }

    function users()
    {
        return $this->hasMany('App\Models\User\User');
    }

    function fuelList()
    {
        return $this->hasMany('App\Models\Company\Fuel');
    }

    function monthly_statistics()
    {
        return $this->hasMany('App\Models\Company\MonthlyStatistics')
            ->orderBy('id', 'desc')
            ->limit(12);
    }
}
