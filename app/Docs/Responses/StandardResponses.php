<?php

namespace App\Docs\Responses;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "GenericMessageResponse",
    description: "قالب استجابة الرسائل العامة والعمليات الناجحة",
    properties: [
        new OA\Property(property: "success", type: "boolean", example: true),
        new OA\Property(property: "message", type: "string", example: "تمت العملية بنجاح.")
    ]
)]
#[OA\Response(
    response: "401Unauthorized",
    description: "رمز الدخول غير صالح أو مفقود",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "success", type: "boolean", example: false),
            new OA\Property(property: "message", type: "string", example: "رمز الدخول غير صالح أو غير مصرح لك بالوصول."),
            new OA\Property(property: "errors", type: "null", example: null)
        ]
    )
)]
#[OA\Response(
    response: "403Forbidden",
    description: "المستخدم لا يملك الصلاحية لتنفيذ هذا الإجراء",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "success", type: "boolean", example: false),
            new OA\Property(property: "message", type: "string", example: "ليس لديك الصلاحية الكافية لتنفيذ هذا الإجراء."),
            new OA\Property(property: "errors", type: "null", example: null)
        ]
    )
)]
#[OA\Response(
    response: "404NotFound",
    description: "العنصر أو المسار المطلوب غير موجود في النظام",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "success", type: "boolean", example: false),
            new OA\Property(property: "message", type: "string", example: "العنصر أو المسار المطلوب غير موجود في النظام."),
            new OA\Property(property: "errors", type: "null", example: null)
        ]
    )
)]
#[OA\Response(
    response: "422ValidationError",
    description: "فشل التحقق من صحة المدخلات المرسلة",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "success", type: "boolean", example: false),
            new OA\Property(property: "message", type: "string", example: "فشل التحقق من صحة المدخلات المرسلة."),
            new OA\Property(
                property: "errors",
                type: "object",
                example: [
                    "name" => ["حقل اسم المدرسة مطلوب ولا يمكن تركه فارغاً."],
                    "code" => ["قيمة كود المدرسة مستخدمة بالفعل، يرجى اختيار قيمة أخرى."]
                ]
            )
        ]
    )
)]
class StandardResponses
{
}
