<?php

namespace App\Http\Resources\Dashboard\Registration;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Dashboard\User\UserResource;
use App\Http\Resources\Dashboard\Registration\RegistrationResource;

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
            'event' => $this->RegistrationResource($this->event),
            'user' => $this->UserResource($this->user),
            'status' => $this->status,
            'joined_at' => $this->joined_at
        ];
    }
}
