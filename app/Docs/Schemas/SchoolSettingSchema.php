<?php

namespace App\Docs\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "SchoolSetting",
    title: "School Setting Model",
    description: "مخطط بيانات إعدادات وهوية واشتراك المدرسة",
    properties: [
        new OA\Property(property: "id", type: "string", format: "uuid", example: "9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6d"),
        new OA\Property(property: "school_id", type: "string", format: "uuid", example: "9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6c"),
        new OA\Property(property: "subscription_plan", type: "string", example: "standard"),
        new OA\Property(property: "subscription_start_date", type: "string", format: "date", example: "2026-09-01"),
        new OA\Property(property: "subscription_end_date", type: "string", format: "date", example: "2027-08-31"),
        new OA\Property(property: "primary_color", type: "string", example: "#1E40AF"),
        new OA\Property(property: "secondary_color", type: "string", example: "#3B82F6"),
        new OA\Property(property: "theme_mode", type: "string", enum: ["light", "dark", "system"], example: "light"),
        new OA\Property(property: "favicon_url", type: "string", nullable: true, example: "https://cdn.school.com/favicon.ico"),
        new OA\Property(property: "timezone", type: "string", example: "Asia/Riyadh"),
        new OA\Property(property: "school_start_time", type: "string", example: "07:30"),
        new OA\Property(property: "school_end_time", type: "string", example: "14:00"),
        new OA\Property(property: "weekend_days", type: "string", example: "friday,saturday"),
        new OA\Property(property: "extra_config", type: "object"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-10-07T10:00:00Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-10-07T10:00:00Z")
    ]
)]
class SchoolSettingSchema
{
}
