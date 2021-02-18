<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class QuotesResource extends JsonResource
{
    /**
     * Transform the resource into an array to show it with many resources.
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
            'author_name' => (string) $this->user->name,
            'updated_ago' => (string) $this->updated_at->diffForHumans()
        ];
    }
}
