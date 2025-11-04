<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class CoffeeWalletsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'beneficiary_id' => $this->beneficiary_id,
            'beneficiary_name' => $this->beneficiary ? $this->beneficiary->name : null,
            'dr_amount' => $this->dr_amount,
            'customer_profile' => new CustomerResource($this->whenLoaded('userInfo')),
            'created_at' => date('m/d/Y H:i:s', strtotime($this->created_at)),
            'updated_at' => date('m/d/Y H:i:s', strtotime($this->updated_at)),
        ];
    }
}
