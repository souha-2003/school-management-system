<?php

return [
    /*
    |--------------------------------------------------------------------------
    | باقات الاشتراك وقيود النظام للمدارس (School Subscription Plans & Quotas)
    |--------------------------------------------------------------------------
    | -1 تعني غير محدود (Unlimited).
    */
    'basic' => [
        'name' => 'الباقة الأساسية',
        'max_students' => 100,
        'max_staff' => 10,
        'features' => ['academic', 'attendance', 'timetable'],
    ],

    'standard' => [
        'name' => 'الباقة القياسية',
        'max_students' => 500,
        'max_staff' => 35,
        'features' => ['academic', 'attendance', 'timetable', 'examinations', 'reports'],
    ],

    'premium' => [
        'name' => 'الباقة المتقدمة',
        'max_students' => -1, // غير محدود
        'max_staff' => -1,    // غير محدود
        'features' => ['*'],  // كافة الميزات
    ],
];
