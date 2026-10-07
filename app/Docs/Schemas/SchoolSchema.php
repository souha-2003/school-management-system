<?php

namespace App\Docs\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "School",
    title: "School Model",
    description: "مخطط بيانات المنشأة التعليمية (المدرسة)",
    properties: [
        new OA\Property(property: "id", type: "string", format: "uuid", example: "9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6c"),
        new OA\Property(property: "name", type: "string", example: "مدارس الرواد النموذجية"),
        new OA\Property(property: "code", type: "string", example: "ROW"),
        new OA\Property(property: "subdomain", type: "string", example: "alrowad"),
        new OA\Property(property: "email", type: "string", example: "info@alrowad.edu.sa"),
        new OA\Property(property: "phone", type: "string", example: "+966501112233"),
        new OA\Property(property: "address", type: "string", example: "المملكة العربية السعودية - الرياض"),
        new OA\Property(property: "logo_url", type: "string", nullable: true, example: "https://cdn.school.com/logo.png"),
        new OA\Property(property: "status", type: "string", enum: ["active", "suspended", "pending_setup"], example: "active"),
        new OA\Property(property: "settings", ref: "#/components/schemas/SchoolSetting"),
        new OA\Property(
            property: "manager_credentials",
            type: "object",
            nullable: true,
            properties: [
                new OA\Property(property: "username", type: "string", example: "ROW-ADM-4821"),
                new OA\Property(property: "initial_password", type: "string", example: "School@9182"),
                new OA\Property(property: "full_name", type: "string", example: "أ. عبد الله المنصور")
            ]
        ),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-10-07T10:00:00Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-10-07T10:00:00Z")
    ]
)]
class SchoolSchema
{
}
