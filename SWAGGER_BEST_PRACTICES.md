# 📐 الدليل القياسي والمعماري لتوثيق واجهات الـ REST API باستخدام Swagger (OpenAPI)
## معايير التوثيق وتصميم الأنظمة لمشاريع Laravel (Official Architecture Guidelines)

---

## 🎯 الهدف من هذا الدليل
يهدف هذا المستند إلى توحيد معايير توثيق واجهات الـ API عبر **Swagger (OpenAPI 3.0)** بين جميع أعضاء الفريق في مشاريع **Laravel**. يلتزم المطور بالمعمارية النظيفة (Clean Architecture) وفصل اهتمامات الكود (Separation of Concerns)، بحيث تبقى المتحكمات (Controllers) نظيفة وخالية من وسوم التوثيق، مع توحيد هياكل البيانات وأكواد الاستجابة.

---

## 📑 الفهرس
1. [قواعد Git وإدارة الملفات المولدة (.gitignore)](#1-قواعد-git-وإدارة-الملفات-المولدة)
2. [التأسيس وضبط حزمة l5-swagger](#2-التأسيس-وضبط-حزمة-l5-swagger)
3. [المعمارية النظيفة وهيكلية مجلد التوثيق (Docs Architecture)](#3-المعمارية-النظيفة-وهيكلية-مجلد-التوثيق)
4. [مخطط التوثيق العام ونظام الأمان (OpenApiSpec)](#4-مخطط-التوثيق-العام-ونظام-الأمان)
5. [المخططات القابلة لإعادة الاستخدام (Reusable Schemas)](#5-المخططات-القابلة-لإعادة-الاستخدام)
6. [توحيد مخرجات الترقيم الصفحي (Pagination Standard)](#6-توحيد-مخرجات-الترقيم-الصفحي)
7. [توحيد قوالب الأخطاء والاستجابة (Standard Responses)](#7-توحيد-قوالب-الأخطاء-والاستجابة)
8. [توثيق العمليات والمنافذ (Endpoints Documentation)](#8-توثيق-العمليات-والمنافذ)
9. [معايير رفع الملفات والبيانات الثنائية (Multipart Uploads)](#9-معايير-رفع-الملفات-والبيانات-الثنائية)
10. [سير العمل اليومي وتوليد التوثيق (Workflow & Commands)](#10-سير-العمل-اليومي-وتوليد-التوثيق)

---

## 1. قواعد Git وإدارة الملفات المولدة

> [!CAUTION]
> **قاعدة صارمة:** يمنع منعاً باتاً رفع ملفات التوثيق الناتجة تلقائياً (`storage/api-docs/`) إلى مستودع Git المشترك.

* **السبب الهندسي:** يتم توليد ملف `storage/api-docs/api-docs.json` تلقائياً عبر الكود. رفعه للمستودع يتسبب في حدوث تضاربات برمجية مستمرة (`Merge Conflicts`) بين المطورين.
* **الإجراء الإلزامي:** يجب التأكد دائماً من وجود السطر التالي داخل ملف `.gitignore`:
```gitignore
/storage/api-docs/
```

---

## 2. التأسيس وضبط حزمة `l5-swagger`

### أ. التثبيت ونشر الإعدادات:
```bash
composer require darkaonline/l5-swagger
php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"
```

### ب. ضبط ملف الإعدادات (`config/l5-swagger.php`):
يجب التأكد من ضبط الخيارات التالية:

1. **مسارات الفحص والمسح (Scan Paths):** شمل كافة مجلدات العمل والموديولات في حال استخدام نظام Modular:
   ```php
   'annotations' => [
       base_path('app'),
       base_path('Modules'), // عند استخدام معمارية الموديولات
   ],
   ```
2. **التوليد التلقائي أثناء التطوير:**
   ```php
   'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', true),
   ```
3. **حفظ حالة المصادقة (Session Persistence):** منع ضياع التوكن عند تحديث المتصفح:
   ```php
   'persist_authorization' => env('L5_SWAGGER_UI_PERSIST_AUTHORIZATION', true),
   ```
4. **تفعيل أداة التصفية والبحث في الواجهة:**
   ```php
   'filter' => env('L5_SWAGGER_UI_FILTERS', true),
   ```
5. **المضيف الديناميكي (Dynamic Host):** دعم العمل على أي سيرفر أو Port مختلف:
   ```php
   'constants' => [
       'L5_SWAGGER_CONST_HOST' => env('L5_SWAGGER_CONST_HOST', env('APP_URL', 'http://localhost:8000')),
   ],
   ```

---

## 3. المعمارية النظيفة وهيكلية مجلد التوثيق

> [!IMPORTANT]
> **مبدأ الفصل:** يحظر كتابة وسوم OpenAPI (`#[OA\...]`) داخل كلاسات الـ Controllers مباشرة. يجب عزل التوثيق في كلاسات مخصصة ضمن المسار `app/Docs/`.

### الهيكل التنظيمي المعتمد داخل المشروع:
```text
app/Docs/
├── OpenApiSpec.php          # بطاقة تعريف الـ API، السيرفر، التوثيق الأمني، والـ Tags
├── Endpoints/               # كلاسات توثيق العمليات لكل موديول أو وحدة وظيفية
│   ├── [Module]Docs.php
│   └── ...
├── Schemas/                 # تعريف هياكل الكائنات والنماذج القابلة لإعادة الاستخدام
│   ├── [Model]Schema.php
│   ├── PaginationMetaSchema.php
│   └── ...
└── Responses/               # قوالب الردود الموحدة وأكواد الأخطاء الشائعة
    └── StandardResponses.php
```

### المبرر التقني لاعتماد كلاسات مخصصة (Dedicated Classes):
* **Single Responsibility Principle (SRP):** يركز الـ Controller بنسبة 100% على منطق العمل والتحكم.
* **عزل وقت التشغيل (Runtime Isolation):** كلاسات التوثيق تُقرأ عبر التحليل الساكن (Static Analysis) أثناء توليد التوثيق فقط، ولا يتم تحميلها كأعباء إضافية في الذاكرة أثناء عمل التطبيق.

---

## 4. مخطط التوثيق العام ونظام الأمان (`OpenApiSpec.php`)

يتم تعريف البيانات الوصفية للمشروع ونظام المصادقة في ملف مركزي واحد:

```php
namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "اسم النظام / اسم الواجهة البرمجية",
    description: "وصف تقني شامل لوظيفة الـ API والخدمات التي يقدمها"
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST . "/api",
    description: "سيرفر البيئة الحالية"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "أدخل رمز التوكن (Bearer Token) مباشرة بدون أي إضافات"
)]
#[OA\Tag(
    name: "اسم الموديول / القسم",
    description: "شرح العمليات التابعة لهذا القسم"
)]
class OpenApiSpec
{
}
```

---

## 5. المخططات القابلة لإعادة الاستخدام (`Schemas`)

تُعرّف خصائص كل نموذج (Model) مرة واحدة داخل مجلد `app/Docs/Schemas/`، وتستخدم كمرجع عبر التعليمة `$ref`:

### نموذج تعريف Schema قياسي:
```php
namespace App\Docs\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ResourceName",
    title: "Resource Model",
    description: "مخطط بيانات الكائن",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "عنوان أو اسم افتراضي"),
        new OA\Property(property: "is_active", type: "boolean", example: true),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-01-01T00:00:00Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-01-01T00:00:00Z"),
        // ربط بمخطط فرعي آخر إن وجد
        new OA\Property(property: "relation", ref: "#/components/schemas/RelatedResourceSchema")
    ]
)]
class ResourceSchema
{
}
```

### استخدام المرجع في أي Endpoint:
```php
#[OA\JsonContent(ref: "#/components/schemas/ResourceName")]
```

---

## 6. توحيد مخرجات الترقيم الصفحي (`Pagination Standard`)

نظراً لأن بنية الاستجابة لدالة `paginate()` موحدة عبر النظام، يُعرّف كائن الترقيم مرة واحدة داخل `app/Docs/Schemas/PaginationMetaSchema.php`:

```php
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
        new OA\Property(property: "first_page_url", type: "string", example: "http://domain.test/api/resource?page=1"),
        new OA\Property(property: "last_page_url", type: "string", example: "http://domain.test/api/resource?page=5"),
        new OA\Property(property: "next_page_url", type: "string", nullable: true, example: "http://domain.test/api/resource?page=2"),
        new OA\Property(property: "prev_page_url", type: "string", nullable: true, example: null),
        new OA\Property(property: "path", type: "string", example: "http://domain.test/api/resource")
    ]
)]
class PaginationMetaSchema
{
}
```

### دمج الترقيم الصفحي مع أي قائمة بيانات:
يتم استخدام تركيب `allOf` لدمج الـ Pagination Meta مع مصفوفة العناصر:
```php
content: new OA\JsonContent(
    allOf: [
        new OA\Schema(ref: "#/components/schemas/PaginationMeta"),
        new OA\Schema(
            properties: [
                new OA\Property(
                    property: "data",
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/ResourceName")
                )
            ]
        )
    ]
)
```

---

## 7. توحيد قوالب الأخطاء والاستجابة (`Standard Responses`)

يتم توحيد مظهر الأخطاء ورسائل العمليات داخل `app/Docs/Responses/StandardResponses.php`:

```php
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
```

---

## 8. توثيق العمليات والمنافذ (`Endpoints`)

يتم توثيق كل وحدة مسارات داخل كلاس مخصص في مجلد `app/Docs/Endpoints/`:

```php
namespace App\Docs\Endpoints;

use OpenApi\Attributes as OA;

class ResourceDocs
{
    #[OA\Get(
        path: "/resource",
        summary: "استعراض قائمة العناصر مع الفلترة والترقيم",
        tags: ["ResourceTag"],
        parameters: [
            new OA\Parameter(
                name: "search",
                in: "query",
                required: false,
                description: "البحث بالاسم أو النص",
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "page",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", default: 1)
            ),
            new OA\Parameter(
                name: "per_page",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", default: 15)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "قائمة العناصر المسترجعة بنجاح",
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: "#/components/schemas/PaginationMeta"),
                        new OA\Schema(
                            properties: [
                                new OA\Property(
                                    property: "data",
                                    type: "array",
                                    items: new OA\Items(ref: "#/components/schemas/ResourceName")
                                )
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(ref: "#/components/responses/401Unauthorized", response: 401)
        ]
    )]
    public function index() {}

    #[OA\Post(
        path: "/resource",
        summary: "إنشاء عنصر جديد",
        tags: ["ResourceTag"],
        security: [["bearerAuth" => []]], // دلالة أن المسار محمي
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "اسم افتراضي"),
                    new OA\Property(property: "description", type: "string", nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "تم الإنشاء بنجاح",
                content: new OA\JsonContent(ref: "#/components/schemas/ResourceName")
            ),
            new OA\Response(ref: "#/components/responses/401Unauthorized", response: 401),
            new OA\Response(ref: "#/components/responses/422ValidationError", response: 422)
        ]
    )]
    public function store() {}
}
```

---

## 9. معايير رفع الملفات والبيانات الثنائية (`Multipart Uploads`)

عند وجود حقول رفع ملفات أو وسائط:
1. يتم تحديد نوع الوسائط دائماً كـ: `mediaType: "multipart/form-data"`.
2. حقل الملف يُعرّف بـ: `type: "string", format: "binary"`.
3. **ملاحظة معمارية هامة لعمليات التعديل (Update):** نظراً لمحدودية PHP في معالجة طلبات `PUT` متعددة الأجزاء (Multipart)، يجب إرسال طلب التعديل كـ `POST` مع إرفاق حقل `_method: "PUT"`:

```php
requestBody: new OA\RequestBody(
    required: true,
    content: new OA\MediaType(
        mediaType: "multipart/form-data",
        schema: new OA\Schema(
            required: ["name", "_method"],
            properties: [
                new OA\Property(property: "_method", type: "string", example: "PUT"),
                new OA\Property(property: "name", type: "string", example: "الاسم المحدث"),
                new OA\Property(property: "attachment", type: "string", format: "binary", description: "الملف المرفوع")
            ]
        )
    )
)
```

---

## 10. سير العمل اليومي وتوليد التوثيق

| الإجراء | الأمر البرمجي | الغرض والوصف |
| :--- | :--- | :--- |
| **توليد التوثيق** | `php artisan l5-swagger:generate` | مسح الـ Attributes وتحديث مواصفات الـ JSON يدوياً. |
| **التوليد التلقائي** | ضبط `.env`: `L5_SWAGGER_GENERATE_ALWAYS=true` | تحديث التوثيق تلقائياً عند طلب صفحة الـ UI أثناء التطوير. |
| **واجهة التوثيق** | الرابط: `http://domain.test/api/documentation` | استعراض التوثيق وتجربة المنافذ عبر المتصفح. |

---

## 📋 قائمة التحقق المعمارية لكل Endpoint جديد (Developer Checklist):
- [ ] هل تم كتابة التوثيق في `app/Docs/Endpoints/` وليس في الـ Controller؟
- [ ] هل تم ربط المسار بالـ `Tag` المناسب له؟
- [ ] هل تم استخدام الـ `securityScheme` إذا كان المسار يتطلب توكن؟
- [ ] هل تستخدم الردود مراجع المخططات الموحدة (`ref`) بدلاً من تكرار كتابة الحقول؟
- [ ] هل تم تضمين حالات الخطأ المتوقعة (`401`, `403`, `404`, `422`)؟
- [ ] هل جميع الحقول تحتوي على أمثلة واضحة (`example`) وقابلة للتجربة؟
