<?php

namespace App\Docs\Endpoints;

use OpenApi\Attributes as OA;

class AuthDocs
{
    #[OA\Post(
        path: "/v1/auth/login",
        summary: "تسجيل الدخول إلى النظام (Multi-Identifier Login)",
        description: "### 🔐 آلية المصادقة وتسجيل الدخول:
* يتيح هذا المنفذ تسجيل الدخول لجميع مستخدمي المنصة (سوبر أدمن، مدراء المدارس، الكادر التعليمي، أولياء الأمور، الطلاب).
* **معرّف الدخول المتعدد (`identifier`):** يمكن للمستخدم الدخول باستخدام أي من:
  * اسم المستخدم (`username`)
  * البريد الإلكتروني (`email`)
  * رقم الهاتف (`phone_number`)
* **التحقق من الأمان والحالة:**
  * يتم رفض الدخول إذا كان حساب المستخدم معطلاً (`suspended` أو `inactive` أو `pending_activation`).
  * في حال كان المستخدم ينتمي لمدرسة، يتم التحقق من أن مدرسته نشطة وليست معلقة (`suspended`).
* **إصدار التوكن:** يُصدر النظام رمز مصادقة آمن (Laravel Sanctum Bearer Token).",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            description: "بيانات اعتماد الدخول للمستخدم",
            content: new OA\JsonContent(
                required: ["identifier", "password"],
                properties: [
                    new OA\Property(
                        property: "identifier",
                        type: "string",
                        description: "🔴 **[إلزامي]** اسم المستخدم أو البريد الإلكتروني أو رقم الهاتف",
                        example: "SUPER-ADMIN"
                    ),
                    new OA\Property(
                        property: "password",
                        type: "string",
                        format: "password",
                        description: "🔴 **[إلزامي]** كلمة مرور الحساب",
                        example: "SuperAdmin@2026!"
                    ),
                    new OA\Property(
                        property: "device_name",
                        type: "string",
                        nullable: true,
                        description: "🟢 **[اختياري]** اسم الجهاز أو المتصفح المستخدم لإصدار التوكن",
                        example: "Chrome on Windows 11"
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "تم تسجيل الدخول بنجاح وإصدار رمز التوكن",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "data", ref: "#/components/schemas/AuthResponse")
                    ]
                )
            ),
            new OA\Response(ref: "#/components/responses/422ValidationError", response: 422),
            new OA\Response(ref: "#/components/responses/401Unauthorized", response: 401)
        ]
    )]
    public function login() {}

    #[OA\Post(
        path: "/v1/auth/logout",
        summary: "تسجيل الخروج وإلغاء رمز الدخول الحالي",
        description: "### 🚪 آلية تسجيل الخروج:
* يقوم بإلغاء وإبطال رمز التوكن (Current Access Token) المستخدم في الجلسة الحالية من قاعدة البيانات فوراً.
* يتطلب ترويسة المصادقة `Authorization: Bearer {token}`.",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "تم تسجيل الخروج بنجاح",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "تم تسجيل الخروج بنجاح.")
                    ]
                )
            ),
            new OA\Response(ref: "#/components/responses/401Unauthorized", response: 401)
        ]
    )]
    public function logout() {}

    #[OA\Get(
        path: "/v1/auth/me",
        summary: "استرجاع بيانات الملف الشخصي للمستخدم الحالي",
        description: "### 👤 بيانات المستخدم الحالي:
* يقوم بإرجاع كافة تفاصيل الحساب الحالي المسجل دخوله مع بيانات المدرسة المرتبطة به إن وجدت.
* يتطلب ترويسة المصادقة `Authorization: Bearer {token}`.",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "بيانات المستخدم الحالي بنجاح",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "data", ref: "#/components/schemas/User")
                    ]
                )
            ),
            new OA\Response(ref: "#/components/responses/401Unauthorized", response: 401)
        ]
    )]
    public function me() {}
}
