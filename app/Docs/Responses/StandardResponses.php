<?php

namespace App\Docs\Responses;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "GenericMessageResponse",
    properties: [
        new OA\Property(property: "message", type: "string", example: "تمت العملية بنجاح")
    ]
)]
#[OA\Response(
    response: "401Unauthorized",
    description: "رمز الدخول غير صالح أو مفقود",
    content: new OA\JsonContent(
        properties: [new OA\Property(property: "message", type: "string", example: "Unauthenticated.")]
    )
)]
#[OA\Response(
    response: "403Forbidden",
    description: "المستخدم لا يملك الصلاحية لتنفيذ هذا الإجراء",
    content: new OA\JsonContent(
        properties: [new OA\Property(property: "message", type: "string", example: "This action is unauthorized.")]
    )
)]
#[OA\Response(
    response: "404NotFound",
    description: "العنصر المطلوب غير موجود في النظام",
    content: new OA\JsonContent(
        properties: [new OA\Property(property: "message", type: "string", example: "Resource not found.")]
    )
)]
#[OA\Response(
    response: "422ValidationError",
    description: "فشل التحقق من صحة المدخلات المرسلة",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "message", type: "string", example: "The given data was invalid."),
            new OA\Property(property: "errors", type: "object")
        ]
    )
)]
class StandardResponses
{
}
