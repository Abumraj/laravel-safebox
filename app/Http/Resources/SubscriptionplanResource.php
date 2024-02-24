<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionplanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
        'planName'=>$this->name,
        'planPrice' =>$this->price,
        'planStorage' => $this->storage,
         'refferal' => $this->refferal,
          'planColor' =>$this->color,
           'planCode' =>$this->code
        ];
    }
}
