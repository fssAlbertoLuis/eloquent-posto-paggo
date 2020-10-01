<?php

namespace App\Http\Resources\Company;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CompanyResource extends JsonResource
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
            'cnpj' => $this->cnpj,
            'user' => $this->when(Auth::user()->permission === 'vital-admin', $this->owner),
            'fuelList' => FuelResource::collection($this->fuelList),
        ];
    }
}
