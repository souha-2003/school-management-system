<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "School Management System API",
    description: "توثيق شامل لواجهات برمجة التطبيقات (RESTful API) الخاصة بنظام إدارة المدرسة الشامل"
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST . "/api",
    description: "خادم البيئة الحالية (Current Environment Server)"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "أدخل رمز التوكن (Bearer Token) مباشرة بدون أي إضافات"
)]
#[OA\Tag(
    name: "Authentication",
    description: "إدارة تسجيل الدخول، الخروج، وتفاصيل المستخدم الحالي"
)]
#[OA\Tag(
    name: "Academic",
    description: "السنوات الدراسية، الفصول (Terms)، المراحل والصفوف، والمواد الدراسية"
)]
#[OA\Tag(
    name: "Students",
    description: "إدارة الطلاب، أولياء الأمور، وسجلات التسجيل السنوي"
)]
#[OA\Tag(
    name: "Teachers",
    description: "إدارة المعلمين وتوزيع المواد والفصول الدراسية"
)]
#[OA\Tag(
    name: "Attendance",
    description: "تسجيل ومتابعة حضور وغياب الطلاب والمعلمين"
)]
#[OA\Tag(
    name: "Timetable",
    description: "إدارة الحصص والجداول المدرسية"
)]
#[OA\Tag(
    name: "Examinations",
    description: "إدارة الامتحانات، رصد الدرجات، والشهادات"
)]
#[OA\Tag(
    name: "Finance",
    description: "الرسوم الدراسية، الفواتير، سندات القبض، والخصومات"
)]
class OpenApiSpec
{
}
