<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PurchaseResource extends JsonResource
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
            'user' => $this->when(Auth::user()->permission === 'user', [
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),
            'fuel_name' => $this->fuel_name,
            'fuel_price' => $this->fuel_price,
            'fuel_cost_price' => $this->when(Auth::user()->permission === 'general-manager', $this->fuel_cost_price),
            'amount' => $this->amount,
            'company' => $this->company ? $this->company->name : null,
            'confirmed' => $this->confirmed,
            'cancelled' =>$this->cancelled,
            'purchase_code' => $this->purchase_code,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
