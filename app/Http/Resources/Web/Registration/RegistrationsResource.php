<?php

namespace App\Http\Resources\Web\Registration;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Web\Event\EventResource;

class RegistrationsResource extends JsonResource
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
            'event' => new EventResource($this->event),
            'status' => $this->status,
            'joined_at' => $this->joined_at
        ];
    }
}
