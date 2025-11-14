<?php

namespace App\Api\PublicQuotes\Resources;

use App\Api\Users\Resources\UserResource;
use Domain\Quotes\Models\Quote;
use Domain\Quotes\States\QuoteState;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Quote
 */
class PublicQuoteResource extends JsonResource
{
    /**
     * Transform the resource into an array to show only itself.
     *
     * @param  Request  $request
     * @return array<string, UserResource|QuoteState|float|int|string|null>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'state' => $this->state,
            'average_rating' => $this->average_score,
            'user' => UserResource::make($this->whenLoaded('user')),
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];
    }
}
