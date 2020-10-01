<?php

namespace App\Http\Resources\User;

use App\Http\Resources\BalanceResource;
use App\Http\Resources\Company\CompanyResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'permission' => $this->permission,
            $this->mergeWhen($this->permission === 'customer', [
                'balance' => new BalanceResource($this->balance),
                'history' => [
                    'purchases' => PurchaseResource::collection($this->purchaseHistory),
                    'recharges' => RechargeResource::collection($this->rechargeHistory),
                ],
            ]),
            'company' => $this->when($this->permission !== 'customer', new CompanyResource($this->company)),
            'is_admin' => $this->permission === 'admin',
            'is_proprietor' => $this->when($this->is_proprietor, true),
            'phone_verified' => $this->phone_verified,
        ];
    }
}
