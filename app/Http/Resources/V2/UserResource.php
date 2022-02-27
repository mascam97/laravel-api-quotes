<?php

namespace App\Http\Resources\V2;

use App\Models\Quote;
use App\Models\User;
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
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => (int) $this->id,
            'name' => (string) $this->name,
            'email' => (string) $this->email,
            'quotes'=> QuoteCollection::make($this->whenLoaded('quotes')),
            'quotes_count' => (int) $this->quotes_count,
            'ratings_count' => (int) $this->ratings(Quote::class)->count(),
            'created_ago' => (string) $this->created_at->diffForHumans(),
        ];
    }
}
