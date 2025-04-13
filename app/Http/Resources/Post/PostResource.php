<?php

declare(strict_types=1);

namespace App\Http\Resources\Post;

use App\Http\Resources\User\UserResource;
use App\Http\Resources\Owner\OwnerResource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'title' => $this->title,
            'content' => $this->content,
            'user' => new UserResource($this->whenLoaded('user')),
            'owner' => new OwnerResource($this->whenLoaded('owner')),
            'is_restricted' => $this->is_restricted,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
