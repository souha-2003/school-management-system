<?php

namespace Modules\Auth\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Tenant\Http\Resources\SchoolResource;

class UserResource extends JsonResource
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
            'school_id' => $this->school_id,
            'username' => $this->username,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'avatar_url' => $this->avatar_url,
            'user_type' => $this->user_type,
            'status' => $this->status,
            'metadata' => $this->metadata ?? [],
            'school' => new SchoolResource($this->whenLoaded('school')),
            'last_login_at' => $this->last_login_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
