<?php

namespace Modules\Tenant\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolSettingResource extends JsonResource
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
            'subscription_plan' => $this->subscription_plan,
            'subscription_start_date' => $this->subscription_start_date?->format('Y-m-d') ?? $this->subscription_start_date,
            'subscription_end_date' => $this->subscription_end_date?->format('Y-m-d') ?? $this->subscription_end_date,
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'theme_mode' => $this->theme_mode,
            'favicon_url' => $this->favicon_url,
            'timezone' => $this->timezone,
            'school_start_time' => $this->school_start_time,
            'school_end_time' => $this->school_end_time,
            'weekend_days' => $this->weekend_days,
            'extra_config' => $this->extra_config ?? [],
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
