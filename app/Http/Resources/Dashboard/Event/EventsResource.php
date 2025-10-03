<?php

namespace App\Http\Resources\Dashboard\Event;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'start_datetime' => $this->start_datetime,
            'duration' => $this->duration,
            'location' => $this->location,
            'capacity' => $this->capacity,
            'status' => $this->status,
            'registrations_count' => $this->registrations->count()
        ];
    }
}
