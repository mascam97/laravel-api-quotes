<?php

namespace App\Api\Users\Resources;

use App\Api\Quotes\Resources\QuoteResource;
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
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => (int) $this->id,
            'name' => (string) $this->name,
            'email' => (string) $this->email,
            'quotes'=> QuoteResource::collection($this->whenLoaded('quotes')),
            'quotesCount'=> $this->when($this->quotes_count, $this->quotes_count),
            'created_at' => (string) $this->created_at,
        ];
    }
}
