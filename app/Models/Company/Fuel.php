<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class Fuel extends Model
{
    protected $table = 'fuel_list';
    protected $fillable = ['name', 'cost_price', 'shop_price', 'app_price'];

    function company()
    {
        return $this->hasOne('App\Models\Company');
    }

    public function getShopPriceAttribute($value)
    {
        return number_format($value, 3);
    }

    public function getAppPriceAttribute($value)
    {
        return number_format($value, 3);
    }

    public function getCostPriceAttribute($value)
    {
        return number_format($value, 3);
    }
}
