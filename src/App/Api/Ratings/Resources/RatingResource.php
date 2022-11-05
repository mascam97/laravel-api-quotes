<?php

namespace App\Api\Ratings\Resources;

use App\Api\Quotes\Resources\QuoteResource;
use App\Api\Users\Resources\UserResource;
use Domain\Rating\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Rating;
 */
class RatingResource extends JsonResource
{
    /**
     * Transform the resource into an array to show only itself.
     *
     * @param  Request  $request
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'score' => $this->score,
            'qualifier_id' => $this->qualifier_id,
            'qualifier_type' => $this->qualifier_type,
            'qualifier' => UserResource::make($this->whenLoaded('qualifier')),
            'rateable_id' => $this->rateable_id,
            'rateable_type' => $this->rateable_type,
            'rateable' => QuoteResource::make($this->whenLoaded('rateable')),
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];
    }
}
