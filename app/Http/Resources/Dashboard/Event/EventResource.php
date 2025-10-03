<?php

namespace App\Http\Resources\Dashboard\Event;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'description' => $this->description,
            'location' => $this->location,
            'capacity' => $this->capacity,
            'waitlist_capacity' => $this->waitlist_capacity,
            'status' => $this->status,
            'registration_stats' => [
                'joined_count' => $this->joinedRegistrations()->count(),
                'waitlist_count' => $this->waitlistedRegistrations()->count(),
                'available_spots' => max(0, $this->capacity - $this->joinedRegistrations()->count()),
                'waitlist_available' => max(0, $this->waitlist_capacity - $this->waitlistedRegistrations()->count()),
                'is_full' => $this->joinedRegistrations()->count() >= $this->capacity,
                'waitlist_full' => $this->waitlistedRegistrations()->count() >= $this->waitlist_capacity
            ],
            'registrations' => $this->registrations
        ];
    }
}
