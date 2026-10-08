<?php

namespace App\Docs\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "AuthResponse",
    title: "Auth Response",
    description: "بيانات الاستجابة عند نجاح تسجيل الدخول",
    properties: [
        new OA\Property(property: "token", type: "string", example: "1|hGEEV5erGTqujtczKbQM1r54k6MbaV70G0M27G5oa6be546f"),
        new OA\Property(property: "token_type", type: "string", example: "Bearer"),
        new OA\Property(property: "user", ref: "#/components/schemas/User")
    ]
)]
class AuthResponseSchema
{
}
