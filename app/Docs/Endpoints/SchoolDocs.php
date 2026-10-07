<?php

namespace App\Docs\Endpoints;

use OpenApi\Attributes as OA;

class SchoolDocs
{
    #[OA\Get(
        path: "/v1/schools",
        summary: "استعراض قائمة المدارس مع الفلترة والترقيم",
        description: "### 📋 دليل الاستعلام والفلترة:
* يتيح هذا المنفذ عرض جميع المدارس المسجلة في النظام مع بيانات الإعدادات والاشتراك المرتبطة بها.
* **البحث الشامل (`search`):** يبحث تلقائياً في (اسم المدرسة، الكود، الرابط الفرعي، البريد الإلكتروني، رقم الهاتف).
* **الفلترة بالحالة (`status`):** يقبل (`active`، `suspended`، `pending_setup`).
* **الترقيم الصفحي:** تدعم الاستجابة معايير لارافيل للترقيم الصفحي مع حقول `links` و `meta`.",
        tags: ["Schools"],
        parameters: [
            new OA\Parameter(name: "search", in: "query", required: false, description: "🔍 كلمة البحث (الاسم، الكود، الرابط، البريد، الهاتف)", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "status", in: "query", required: false, description: "📌 تصفية حسب الحالة (`active`, `suspended`, `pending_setup`)", schema: new OA\Schema(type: "string", enum: ["active", "suspended", "pending_setup"])),
            new OA\Parameter(name: "page", in: "query", required: false, description: "📄 رقم الصفحة المطلوب عرضها", schema: new OA\Schema(type: "integer", default: 1)),
            new OA\Parameter(name: "per_page", in: "query", required: false, description: "🔢 عدد النتائج في الصفحة الواحدة (افتراضياً 15)", schema: new OA\Schema(type: "integer", default: 15))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "قائمة المدارس بنجاح مع بيانات الترقيم",
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: "#/components/schemas/PaginationMeta"),
                        new OA\Schema(
                            properties: [
                                new OA\Property(property: "data", type: "array", items: new OA\Items(ref: "#/components/schemas/School"))
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(ref: "#/components/responses/401Unauthorized", response: 401),
            new OA\Response(ref: "#/components/responses/403Forbidden", response: 403)
        ]
    )]
    public function index() {}

    #[OA\Post(
        path: "/v1/schools",
        summary: "إنشاء مدرسة جديدة مع إعداداتها وحساب المدير المبدئي",
        description: "### 🏫 تفاصيل إنشاء المدرسة وحساب المدير المدمج:
تقوم هذه العملية بإنشاء المدرسة وكافة ملحقاتها داخل **Database Transaction** موحدة:
1. **بيانات المنشأة الأساسية:** الاسم والكود إجباريان، وبقية الحقول اختيارية.
2. **إعدادات وهوية المدرسة (`settings`):** اختيارية؛ وفي حال عدم إرسالها تُطبق القيم الافتراضية للنظام (باقة standard، اشتراك لمدة سنة، توقيت الرياض).
3. **حساب مدير المدرسة (`manager`):** اختياري؛ عند تزويد بياناته، يتم تلقائياً:
   * توليد اسم مستخدم فريد له بصيغة: `{CODE}-ADM-{RANDOM}`
   * إنشاء حساب دخول له بنوع `staff` وتشفير كلمة مروره
   * إنشاء ملف وظيفي في جدول الكادر الوظيفي `staff` بمسمى **مدير المدرسة**
   * إرجاع بيانات الدخول المبدئية (`manager_credentials`) في الاستجابة لمشاركتها مع المدير.",
        tags: ["Schools"],
        requestBody: new OA\RequestBody(
            required: true,
            description: "بيانات إنشاء المدرسة الجديدة مع إعدادات الاشتراك وحساب المدير",
            content: new OA\JsonContent(
                required: ["name", "code"],
                properties: [
                    // 1. بيانات المدرسة الأساسية
                    new OA\Property(property: "name", type: "string", description: "🔴 **[إلزامي]** اسم المدرسة أو المجمع التعليمي الرسمي (حتى 255 حرف)", example: "مدارس الرواد الأهلية"),
                    new OA\Property(property: "code", type: "string", description: "🔴 **[إلزامي | فريد]** كود مختصر بالإنجليزية للمدرسة يُبنى عليه توليد المعرفات (مثل: ROW, SCH01)", example: "ROW"),
                    new OA\Property(property: "subdomain", type: "string", nullable: true, description: "🟢 **[اختياري | فريد]** الرابط الفرعي المخصص للمدرسة (حروف وأرقام وشرطات فقط)", example: "alrowad"),
                    new OA\Property(property: "email", type: "string", nullable: true, description: "🟢 **[اختياري]** البريد الإلكتروني الرسمي المعتمد للتواصل والمراسلات", example: "info@alrowad.edu.sa"),
                    new OA\Property(property: "phone", type: "string", nullable: true, description: "🟢 **[اختياري]** رقم هاتف المدرسة الرسمي للتواصل", example: "+966501112233"),
                    new OA\Property(property: "address", type: "string", nullable: true, description: "🟢 **[اختياري]** العنوان الجغرافي ومقر المنشأة", example: "الرياض - حي الياسمين"),
                    new OA\Property(property: "logo_url", type: "string", nullable: true, description: "🟢 **[اختياري]** رابط شعار المدرسة المباشر (URL)", example: "https://cdn.school.com/logo.png"),
                    new OA\Property(property: "status", type: "string", enum: ["active", "suspended", "pending_setup"], description: "🟢 **[اختياري]** حالة التشغيل المبدئية للمدرسة (الافتراضي: active)", example: "active"),

                    // 2. إعدادات وهوية واشتراك المدرسة
                    new OA\Property(
                        property: "settings",
                        type: "object",
                        description: "⚙️ **[اختياري]** كائن الإعدادات والاشتراك (إذا لم يُرسل يتم تعيين قيم افتراضية تلقائياً)",
                        properties: [
                            new OA\Property(property: "subscription_plan", type: "string", description: "📦 باقة الاشتراك السحابي (مثل: `trial`, `standard`, `enterprise`)", example: "standard"),
                            new OA\Property(property: "subscription_start_date", type: "string", format: "date", description: "📅 تاريخ بداية فترة الاشتراك والتأجير (`YYYY-MM-DD`)", example: "2026-09-01"),
                            new OA\Property(property: "subscription_end_date", type: "string", format: "date", description: "📅 تاريخ انتهاء فترة الاشتراك (`YYYY-MM-DD`)", example: "2027-08-31"),
                            new OA\Property(property: "primary_color", type: "string", description: "🎨 اللون الأساسي لهوية المدرسة (Hex Code)", example: "#1E40AF"),
                            new OA\Property(property: "secondary_color", type: "string", description: "🎨 اللون الثانوي للهوية (Hex Code)", example: "#3B82F6"),
                            new OA\Property(property: "theme_mode", type: "string", enum: ["light", "dark", "system"], description: "🌓 نمط العرض للواجهة (`light`, `dark`, `system`)", example: "light"),
                            new OA\Property(property: "favicon_url", type: "string", nullable: true, description: "🌐 رابط أيقونة المتصفح للمدرسة (Favicon URL)", example: "https://cdn.school.com/favicon.ico"),
                            new OA\Property(property: "timezone", type: "string", description: "⏰ المنطقة الزمنية المعتمدة (مثل: `Asia/Riyadh`)", example: "Asia/Riyadh"),
                            new OA\Property(property: "school_start_time", type: "string", description: "⏰ موعد بداية اليوم الدراسي والطابور الصباحي (`H:i` أو `H:i:s`)", example: "07:30"),
                            new OA\Property(property: "school_end_time", type: "string", description: "⏰ موعد انصراف ونهاية اليوم الدراسي (`H:i` أو `H:i:s`)", example: "14:00"),
                            new OA\Property(property: "weekend_days", type: "string", description: "🏖️ أيام العطلة الأسبوعية مفصولة بفاصلة (مثل: `friday,saturday`)", example: "friday,saturday"),
                            new OA\Property(property: "extra_config", type: "object", description: "🔧 كائن مخصص لأي إعدادات إضافية مرنة مستقبلاً (JSON)", example: ["sms_notifications" => true])
                        ]
                    ),

                    // 3. حساب مدير المدرسة المدمج
                    new OA\Property(
                        property: "manager",
                        type: "object",
                        description: "👤 **[اختياري]** كائن بيانات مدير المدرسة الأساسي لتأسيس حسابه وملفه الوظيفي تلقائياً",
                        properties: [
                            new OA\Property(property: "full_name", type: "string", description: "🔴 **[مطلوب عند إرسال المدير]** الاسم الكامل لمدير المدرسة", example: "أ. عبد الله المنصور"),
                            new OA\Property(property: "phone_number", type: "string", description: "🔴 **[مطلوب عند إرسال المدير]** رقم الهاتف المعتمد للدخول والتواصل", example: "+966559988776"),
                            new OA\Property(property: "email", type: "string", nullable: true, description: "🟢 **[اختياري]** البريد الإلكتروني الخاص بحساب المدير", example: "manager@alrowad.edu.sa"),
                            new OA\Property(property: "password", type: "string", nullable: true, description: "🟢 **[اختياري]** كلمة المرور المبدئية (إذا تركت فارغة يُولد النظام كلمة سر عشوائية آمنة)", example: "Secret1234!")
                        ]
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "تم إنشاء المدرسة بنجاح مع إرجاع بيانات الدخول المبدئية للمدير",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "data", ref: "#/components/schemas/School")
                    ]
                )
            ),
            new OA\Response(ref: "#/components/responses/422ValidationError", response: 422),
            new OA\Response(ref: "#/components/responses/401Unauthorized", response: 401)
        ]
    )]
    public function store() {}

    #[OA\Get(
        path: "/v1/schools/{school}",
        summary: "استعراض تفاصيل مدرسة محددة",
        description: "### 🔍 عرض تفاصيل المدرسة:
* يجلب بيانات المنشأة التعليمية بالمعرف المميز لها (`UUID`).
* يدمج تفاصيل الإعدادات والاشتراك تلقائياً في كائن `settings`.",
        tags: ["Schools"],
        parameters: [
            new OA\Parameter(name: "school", in: "path", required: true, description: "🆔 معرف المدرسة الأساسي (UUID)", schema: new OA\Schema(type: "string", format: "uuid"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "بيانات المدرسة المطلوبة مع الإعدادات",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "data", ref: "#/components/schemas/School")
                    ]
                )
            ),
            new OA\Response(ref: "#/components/responses/404NotFound", response: 404),
            new OA\Response(ref: "#/components/responses/401Unauthorized", response: 401)
        ]
    )]
    public function show() {}

    #[OA\Put(
        path: "/v1/schools/{school}",
        summary: "تعديل بيانات المدرسة الأساسية",
        description: "### ✏️ تعديل بيانات المدرسة:
* يدعم التعديل الجزئي؛ أرسل فقط الحقول التي تريد تعديلها.
* يتم تجاهل سجل المدرسة الحالي تلقائياً عند فحص تفرد الكود (`code`) والرابط (`subdomain`).",
        tags: ["Schools"],
        parameters: [
            new OA\Parameter(name: "school", in: "path", required: true, description: "🆔 معرف المدرسة (UUID)", schema: new OA\Schema(type: "string", format: "uuid"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: "الحقول المطلوب تعديلها للمدرسة",
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", description: "اسم المدرسة الجديد", example: "مدارس الرواد الأهلية الحديثة"),
                    new OA\Property(property: "code", type: "string", description: "كود المدرسة الجديد (فريد)", example: "ROW2"),
                    new OA\Property(property: "subdomain", type: "string", nullable: true, description: "الرابط الفرعي الجديد للمدرسة", example: "rowad-new"),
                    new OA\Property(property: "email", type: "string", nullable: true, description: "البريد الإلكتروني المحدث", example: "contact@alrowad.edu.sa"),
                    new OA\Property(property: "phone", type: "string", description: "رقم الهاتف المحدث", example: "+966501119999"),
                    new OA\Property(property: "address", type: "string", description: "العنوان المحدث", example: "الرياض - حي النرجس"),
                    new OA\Property(property: "logo_url", type: "string", nullable: true, description: "رابط الشعار المحدث", example: "https://cdn.school.com/new-logo.png"),
                    new OA\Property(property: "status", type: "string", enum: ["active", "suspended", "pending_setup"], description: "حالة المدرسة", example: "active")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "تم تحديث بيانات المدرسة بنجاح",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "data", ref: "#/components/schemas/School")
                    ]
                )
            ),
            new OA\Response(ref: "#/components/responses/422ValidationError", response: 422),
            new OA\Response(ref: "#/components/responses/404NotFound", response: 404)
        ]
    )]
    public function update() {}

    #[OA\Patch(
        path: "/v1/schools/{school}/status",
        summary: "تغيير حالة تشغيل المدرسة (نشطة / معلقة / قيد الإعداد)",
        description: "### 🚦 إدارة حالة المنشأة:
يتيح تغيير حالة المدرسة التشغيلية بشكل سريع:
* `active`: المدرسة نشطة وجميع مستخدميها يستطيعون الدخول.
* `suspended`: المدرسة معلقة (مثلاً لانتهاء الاشتراك السحابي) ويُمنع مستخدموها من تسجيل الدخول.
* `pending_setup`: المدرسة قيد الإعداد المبدئي والتجهيز الأكاديمي.",
        tags: ["Schools"],
        parameters: [
            new OA\Parameter(name: "school", in: "path", required: true, description: "🆔 معرف المدرسة (UUID)", schema: new OA\Schema(type: "string", format: "uuid"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: "الحالة التشغيلية الجديدة للمدرسة",
            content: new OA\JsonContent(
                required: ["status"],
                properties: [
                    new OA\Property(property: "status", type: "string", enum: ["active", "suspended", "pending_setup"], description: "🔴 **[إلزامي]** الحالة الجديدة (`active` أو `suspended` أو `pending_setup`)", example: "suspended")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "تم تحديث حالة تشغيل المدرسة بنجاح",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "data", ref: "#/components/schemas/School")
                    ]
                )
            ),
            new OA\Response(ref: "#/components/responses/422ValidationError", response: 422),
            new OA\Response(ref: "#/components/responses/404NotFound", response: 404)
        ]
    )]
    public function changeStatus() {}

    #[OA\Delete(
        path: "/v1/schools/{school}",
        summary: "الحذف الناعم للمدرسة (Soft Delete)",
        description: "### 🗑️ أرشفة وحذف المدرسة ناعماً:
* يتم وضع علامة الحذف (`deleted_at`) مع الاحتفاظ بالبيانات التاريخية في قاعدة البيانات.
* يتم تحرير كود المدرسة ورابطها الفرعي للسماح بإعادة استخدامهما مستقبلاً بأمان عبر مؤشر الـ `active_flag`.",
        tags: ["Schools"],
        parameters: [
            new OA\Parameter(name: "school", in: "path", required: true, description: "🆔 معرف المدرسة (UUID)", schema: new OA\Schema(type: "string", format: "uuid"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "تم حذف المدرسة ناعماً بنجاح",
                content: new OA\JsonContent(ref: "#/components/schemas/GenericMessageResponse")
            ),
            new OA\Response(ref: "#/components/responses/404NotFound", response: 404)
        ]
    )]
    public function destroy() {}

    #[OA\Get(
        path: "/v1/schools/{school}/settings",
        summary: "استعراض إعدادات وهوية وتوقيت الدوام والاشتراك للمدرسة",
        description: "### ⚙️ عرض إعدادات المدرسة:
يجلب تفاصيل الاشتراك السحابي، الهوية البصرية والألوان، توقيت الحصص والدوام الصباحي، وأيام العطلة الأسبوعية.",
        tags: ["School Settings"],
        parameters: [
            new OA\Parameter(name: "school", in: "path", required: true, description: "🆔 معرف المدرسة (UUID)", schema: new OA\Schema(type: "string", format: "uuid"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "إعدادات وهوية المدرسة بنجاح",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "data", ref: "#/components/schemas/SchoolSetting")
                    ]
                )
            ),
            new OA\Response(ref: "#/components/responses/404NotFound", response: 404)
        ]
    )]
    public function showSettings() {}

    #[OA\Put(
        path: "/v1/schools/{school}/settings",
        summary: "تعديل إعدادات وهوية وتوقيت الدوام والاشتراك للمدرسة",
        description: "### 🎨 تعديل وتخصيص إعدادات المدرسة:
* يدعم التعديل الجزئي؛ يمكنك إرسال الألوان فقط، أو مواعيد الدوام فقط، أو تمديد فترة الاشتراك.
* في حال لم تكن الإعدادات منشأة مسبقاً، يتم إنشاؤها تلقائياً للمدرسة.",
        tags: ["School Settings"],
        parameters: [
            new OA\Parameter(name: "school", in: "path", required: true, description: "🆔 معرف المدرسة (UUID)", schema: new OA\Schema(type: "string", format: "uuid"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: "الإعدادات المطلوب تحديثها (جميع الحقول تقبل التعديل الجزئي)",
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "subscription_plan", type: "string", description: "📦 باقة الاشتراك السحابي (`trial`, `standard`, `enterprise`)", example: "enterprise"),
                    new OA\Property(property: "subscription_start_date", type: "string", format: "date", description: "📅 تاريخ بداية الاشتراك (`YYYY-MM-DD`)", example: "2026-09-01"),
                    new OA\Property(property: "subscription_end_date", type: "string", format: "date", description: "📅 تاريخ انتهاء الاشتراك (`YYYY-MM-DD` يجب أن يكون بعد تاريخ البداية)", example: "2027-08-31"),
                    new OA\Property(property: "primary_color", type: "string", description: "🎨 اللون الأساسي للثيم (Hex Code)", example: "#10B981"),
                    new OA\Property(property: "secondary_color", type: "string", description: "🎨 اللون الثانوي للثيم (Hex Code)", example: "#059669"),
                    new OA\Property(property: "theme_mode", type: "string", enum: ["light", "dark", "system"], description: "🌓 نمط العرض (`light`, `dark`, `system`)", example: "light"),
                    new OA\Property(property: "favicon_url", type: "string", nullable: true, description: "🌐 رابط أيقونة المتصفح المحدث", example: "https://cdn.school.com/favicon.ico"),
                    new OA\Property(property: "timezone", type: "string", description: "⏰ المنطقة الزمنية المعتمدة", example: "Asia/Riyadh"),
                    new OA\Property(property: "school_start_time", type: "string", description: "⏰ موعد بداية اليوم الدراسي الصباحي (`H:i` أو `H:i:s`)", example: "07:15"),
                    new OA\Property(property: "school_end_time", type: "string", description: "⏰ موعد نهاية الدوام والانصراف (`H:i` أو `H:i:s`)", example: "13:45"),
                    new OA\Property(property: "weekend_days", type: "string", description: "🏖️ أيام العطلة الأسبوعية (مثل `friday,saturday`)", example: "friday,saturday"),
                    new OA\Property(property: "extra_config", type: "object", description: "🔧 مصفوفة أو كائن إعدادات إضافية مرنة (JSON)", example: ["sms_enabled" => true, "max_sessions_per_day" => 7])
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "تم تحديث إعدادات المدرسة بنجاح",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "data", ref: "#/components/schemas/SchoolSetting")
                    ]
                )
            ),
            new OA\Response(ref: "#/components/responses/422ValidationError", response: 422),
            new OA\Response(ref: "#/components/responses/404NotFound", response: 404)
        ]
    )]
    public function updateSettings() {}
}
