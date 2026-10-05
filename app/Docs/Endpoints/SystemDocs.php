<?php

namespace App\Docs\Endpoints;

use OpenApi\Attributes as OA;

class SystemDocs
{
    #[OA\Get(
        path: "/health",
        summary: "فحص حالة عمل الخادم والنظام (System Health Check)",
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200,
                description: "الخدمة تعمل بنجاح",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "ok"),
                        new OA\Property(property: "message", type: "string", example: "School System API is healthy")
                    ]
                )
            )
        ]
    )]
    public function healthCheck(): void
    {
    }
}
