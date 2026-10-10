<?php

namespace App\Docs\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "User",
    title: "User Model",
    description: "مخطط بيانات المستخدم في النظام",
    properties: [
        new OA\Property(property: "id", type: "string", format: "uuid", example: "8b34ed2c-f01c-4a69-8c4c-b9b3f121dda6"),
        new OA\Property(property: "school_id", type: "string", format: "uuid", nullable: true, example: "9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6c"),
        new OA\Property(property: "username", type: "string", example: "ROW-ADM-5421"),
        new OA\Property(property: "email", type: "string", format: "email", nullable: true, example: "manager@alrowad.edu.sa"),
        new OA\Property(property: "phone_number", type: "string", nullable: true, example: "+966501122334"),
        new OA\Property(property: "avatar_url", type: "string", nullable: true, example: null),
        new OA\Property(
            property: "user_type",
            type: "string",
            enum: ["super_admin", "school_admin", "staff", "student", "parent"],
            example: "school_admin"
        ),
        new OA\Property(
            property: "status",
            type: "string",
            enum: ["active", "suspended", "pending_activation", "inactive"],
            example: "active"
        ),
        new OA\Property(
            property: "metadata",
            type: "object",
            nullable: true,
            example: ["title" => "Platform Super Admin", "is_system_owner" => true]
        ),
        new OA\Property(property: "school", ref: "#/components/schemas/School", nullable: true),
        new OA\Property(property: "last_login_at", type: "string", format: "date-time", nullable: true, example: "2026-10-08T08:11:31Z"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-10-08T08:07:34Z")
    ]
)]
class UserSchema
{
}
