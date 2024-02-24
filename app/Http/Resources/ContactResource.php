<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'displayName' => $this->display_name,
            'name' => $this->name,
            'phones' => [
                'phone1' => $this->phone1,
                'phone2' => $this->phone2,
                'phone3' => $this->phone3,
            ],
            'emails' => [
                'email1' => $this->email1,
                'email2' => $this->email2,
                'email3' => $this->email3,
            ],
            'addresses' => [
                'address1' => $this->address1,
                'address2' => $this->address2,
            ],
            // Add more fields as needed
        ];

    }
}
