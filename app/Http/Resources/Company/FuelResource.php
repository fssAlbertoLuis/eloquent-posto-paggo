<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class FuelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'cost_price' => $this->when(Auth::user()->permission === 'general-manager', $this->cost_price),
            'shop_price' => $this->shop_price,
            'app_price' => $this->app_price,
        ];
    }
}
