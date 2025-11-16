<?php

namespace App\Api\Users\Resources;

use App\Api\Quotes\Resources\QuoteResource;
use Domain\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

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
            'quotes' => QuoteResource::collection($this->whenLoaded('quotes')),
            'quotesCount' => $this->when(
                array_key_exists('quotes_count', $this->getAttributes()),
                fn () => $this->getAttribute('quotes_count')
            ),
            'created_at' => (string) $this->created_at,
        ];
    }
}
