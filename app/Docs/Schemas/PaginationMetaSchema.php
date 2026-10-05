<?php

namespace App\Docs\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "PaginationMeta",
    title: "Laravel Pagination Metadata",
    description: "بيانات الترقيم الصفحي الافتراضية في لارافيل",
    properties: [
        new OA\Property(property: "current_page", type: "integer", example: 1),
        new OA\Property(property: "last_page", type: "integer", example: 5),
        new OA\Property(property: "per_page", type: "integer", example: 15),
        new OA\Property(property: "total", type: "integer", example: 75),
        new OA\Property(property: "first_page_url", type: "string", example: "http://localhost:8000/api/resource?page=1"),
        new OA\Property(property: "last_page_url", type: "string", example: "http://localhost:8000/api/resource?page=5"),
        new OA\Property(property: "next_page_url", type: "string", nullable: true, example: "http://localhost:8000/api/resource?page=2"),
        new OA\Property(property: "prev_page_url", type: "string", nullable: true, example: null),
        new OA\Property(property: "path", type: "string", example: "http://localhost:8000/api/resource")
    ]
)]
class PaginationMetaSchema
{
}
