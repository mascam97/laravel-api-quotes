<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\JsonResource;

class UserQuotesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'title' => (string) $this->title,
            'excerpt' => (string) $this->excerpt,
            'rating' => (array) [
                'average' => (float) $this->averageRating(\App\Models\User::class),
                'qualifiers' => (int) $this->qualifiers(\App\Models\User::class)->count(),
            ],
            'created_ago' => (string) $this->updated_at->diffForHumans(),
            'updated_ago' => (string) $this->updated_at->diffForHumans()
        ];
    }
}
