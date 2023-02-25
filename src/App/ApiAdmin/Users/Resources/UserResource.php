<?php

namespace App\ApiAdmin\Users\Resources;

use App\ApiAdmin\Permissions\Resources\PermissionResource;
use App\ApiAdmin\Roles\Resources\RoleResource;
use Domain\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User;
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
            'permissions_count' => $this->when($this->permissions_count !== null, $this->permissions_count),
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'roles_count' => $this->when($this->roles_count !== null, $this->roles_count),
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];
    }
}
