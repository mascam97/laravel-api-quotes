<?php

namespace App\Http\Resources\V2;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Quote;
 */
class QuoteResource extends JsonResource
{
    /**
     * Transform the resource into an array to show only itself.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        // to get the rate by the logged user
        $user = $this->qualifiers(User::class)
        ->where(
            'qualifier_id',
            $request->user()->id
        )->get();

        return [
            'id' => (int) $this->id,
            'title' => (string) $this->title,
            'content' => (string) $this->content,
            'author' => (array) [
                'name' => (string) $this->user->name,
                'email' => (string) $this->user->email,
            ],
            'rating' => (array) [
                // get the score given the logged user
                // If the user has not rated, the score is 0
                'score_by_user' => $user[0]->pivot->score ?? 0,
                'average' => (float) $this->averageRating(User::class),
                'qualifiers' => (int) $this->qualifiers(User::class)->count(),
            ],
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];
    }
}
