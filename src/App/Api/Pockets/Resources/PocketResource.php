<?php

namespace App\Api\Pockets\Resources;

use Domain\Pockets\Models\Pocket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @mixin Pocket;
 */
class PocketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, Carbon|int|string>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'balance' => $this->balance,
            'currency' => $this->currency,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ];
    }
}
