<?php

namespace App\Http\Resources;

use App\Models\file;
use App\Models\subscriptionplan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $plan = subscriptionplan::find($this->subscriptionplan_id);
        $file = new File();
        $rootFile = File::query()->whereIsRoot()->where('created_by', Auth::id())->firstOrFail();
    
        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "country" => $this->country,
            "picture" => $this->picture,
            "phone" => $this->phone_number,
            "usedStorage" => $this->getFileSize($rootFile->descendants()->sum('size')),
            "totalStorage" => $plan->storage,
            "planName" => $plan->name,
            "whatsappCount" => $file->countWhatsapp(),
            "sizeWhatsapp" => $this->getFileSize($file->sizeWhatsapp()),
            "documentCount" => $file->countDocuments(),
            "sizeDocument" => $this->getFileSize($file->sizeDocuments()),
            "photoCount" => $file->countPhotos(),
            "sizePhoto" => $this->getFileSize($file->sizePhotos()),
            "videoCount" => $file->countVideos(),
            "sizeVideo" => $this->getFileSize($file->sizeVideos()),
            "audioCount" => $file->countAudios(),
            "sizeAudio" => $this->getFileSize($file->sizeAudios()),
        ];
    }
    
    protected function getFileSize($size)
    {
        // Convert the size to human-readable format (e.g., KB, MB, GB, etc.)
        // You can implement this logic or use any existing package for this purpose
        // Here's a simple implementation for demonstration:
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $size >= 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        return round($size, 2) . ' ' . $units[$i];
    }
}


