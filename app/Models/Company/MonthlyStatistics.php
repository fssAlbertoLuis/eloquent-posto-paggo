<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class MonthlyStatistics extends Model
{
    protected $fillable = ['month', 'year', 'total_earnings']                                                                          ;

    function company()
    {
        return $this->belongsTo('App\Models\Company\Company');
    }
}
