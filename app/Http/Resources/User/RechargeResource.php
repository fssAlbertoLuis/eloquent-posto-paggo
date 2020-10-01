<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class RechargeResource extends JsonResource
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
            'amount' => $this->amount,
            $this->mergeWhen(Auth::user()->permission !== 'customer', [
               'customer' => $this->user,
                'user' => $this->vendor,
            ]),
            'pending' => $this->pending,
            'created_at' => $this->created_at,
        ];
    }
}
