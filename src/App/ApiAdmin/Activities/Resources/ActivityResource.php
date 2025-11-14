<?php

namespace App\ApiAdmin\Activities\Resources;

use App\ApiAdmin\Users\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Activitylog\Models\Activity;

/**
 * @mixin Activity
 */
class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, UserResource|int|string|null>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'log_name' => $this->log_name,
            'description' => $this->description,
            'subject_type' => $this->subject_type,
            'subject_id' => $this->subject_id,
            'subject' => UserResource::make($this->whenLoaded('subject')), // TODO: The resource should be generic
            'causer_type' => $this->causer_type,
            'causer_id' => $this->causer_id,
            'causer' => UserResource::make($this->whenLoaded('causer')),
            'event' => $this->event,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];
    }
}
