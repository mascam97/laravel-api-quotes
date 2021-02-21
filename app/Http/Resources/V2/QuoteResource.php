<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\JsonResource;

class QuoteResource extends JsonResource
{
    /**
     * Transform the resource into an array to show only itself.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'title' => (string) $this->title,
            'content' => (string) $this->content,
            'author' => (array) [
                'name' => (string) $this->user->name,
                'email' => (string) $this->user->email
            ],
            'rating' => (array) [
                'average' => (float) $this->averageRating(\App\Models\User::class),
                'qualifiers' => (int) $this->qualifiers(\App\Models\User::class)->count(),
            ],
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at
        ];
    }
}
