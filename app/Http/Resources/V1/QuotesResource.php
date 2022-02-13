<?php

namespace App\Http\Resources\V1;

use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Quote;
 */
class QuotesResource extends JsonResource
{
    /**
     * Transform the resource into an array to show it with many resources.
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
            'author_name' => (string) $this->user->name,
            'updated_ago' => (string) $this->updated_at->diffForHumans(),
        ];
    }
}
