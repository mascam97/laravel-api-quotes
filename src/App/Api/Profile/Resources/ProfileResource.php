<?php

namespace App\Api\Profile\Resources;

use App\Api\Pockets\Resources\PocketResource;
use Domain\Users\Enums\SexEnum;
use Domain\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User;
 */
class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, PocketResource|SexEnum|int|string|null>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'locale' => $this->locale,
            'sex' => $this->sex,
            'pocket' => PocketResource::make($this->whenLoaded('pocket')),
            'updated_at' => (string) $this->updated_at,
            'created_at' => (string) $this->created_at,
        ];
    }
}
