<?php

namespace App\Http\Resources\V1;

use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Quote;
 */
class UserQuotesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => (int) $this->id,
            'title' => (string) $this->title,
            'excerpt' => (string) $this->excerpt,
            'created_ago' => (string) $this->updated_at->diffForHumans(),
            'updated_ago' => (string) $this->updated_at->diffForHumans()
        ];
    }
}
