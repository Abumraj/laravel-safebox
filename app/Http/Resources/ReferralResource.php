<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReferralResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = User::find($this->referred_user_id);
      
        return [
            'id' => $this->id,
            'name' => $user->name,
            'picture' => $user->picture,
            // 'referred_user_id' => $this->referred_user_id,
            'status' => $this->status,
            'updatedAt' => $this->updated_at,
        ];
    
    }
}
