<?php

namespace App\ApiAdmin\Users\Resources;

use App\ApiAdmin\Permissions\Resources\PermissionResource;
use App\ApiAdmin\Pockets\Resources\PocketResource;
use App\ApiAdmin\Roles\Resources\RoleResource;
use Domain\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
            'permissions_count' => $this->when(
                array_key_exists('permissions_count', $this->getAttributes()),
                fn () => $this->getAttribute('permissions_count')
            ),
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'roles_count' => $this->when(
                array_key_exists('roles_count', $this->getAttributes()),
                fn () => $this->getAttribute('roles_count')
            ),
            'pocket' => new PocketResource($this->whenLoaded('pocket')),
            'deleted_at' => $this->when(
                array_key_exists('deleted_at', $this->getAttributes()),
                fn () => $this->getAttribute('deleted_at')
            ),
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];
    }
}
