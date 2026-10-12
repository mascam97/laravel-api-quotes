<?php

declare(strict_types=1);

namespace App\Api\Profile\Resources;

use App\Api\Pockets\Resources\PocketResource;
use Domain\Users\Enums\SexEnum;
use Domain\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, PocketResource|SexEnum|int|string|bool|null>
     */
    public function toArray($request): array
    {
        $attributes = $this->getAttributes();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'locale' => $this->locale,
            'sex' => $this->sex,
            'birthday' => array_key_exists('birthday', $attributes) ? $this->birthday?->toDateString() : null,
            'email_verified' => array_key_exists('email_verified_at', $attributes)
                && $this->email_verified_at !== null,
            'pocket' => PocketResource::make($this->whenLoaded('pocket')),
            'updated_at' => (string) $this->updated_at,
            'created_at' => (string) $this->created_at,
        ];
    }
}
