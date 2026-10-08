<?php

return [

    /*
    |--------------------------------------------------------------------------
    | رسائل التحقق من صحة البيانات (Validation Language Lines)
    |--------------------------------------------------------------------------
    |
    | تحتوي هذه المصفوفة على رسائل الأخطاء الافتراضية لقواعد التحقق في لارافيل،
    | ومترجمة باللغة العربية بدقة لتناسب نظام إدارة المدرسة.
    |
    */

    'accepted'             => 'يجب قبول :attribute.',
    'accepted_if'          => 'يجب قبول :attribute عندما يكون :other هو :value.',
    'active_url'           => ':attribute لا يمثل رابطاً صحيحاً.',
    'after'                => 'يجب أن يكون :attribute تاريخاً لاحقاً لتاريخ :date.',
    'after_or_equal'       => 'يجب أن يكون :attribute تاريخاً لاحقاً أو مطابقاً لتاريخ :date.',
    'alpha'                => 'يجب أن يحتوي :attribute على أحرف فقط.',
    'alpha_dash'           => 'يجب أن يحتوي :attribute على أحرف وأرقام وشرطات فقط بدون مسافات.',
    'alpha_num'            => 'يجب أن يحتوي :attribute على أحرف وأرقام فقط.',
    'array'                => 'يجب أن يكون :attribute مصفوفة.',
    'before'               => 'يجب أن يكون :attribute تاريخاً سابقاً لتاريخ :date.',
    'before_or_equal'      => 'يجب أن يكون :attribute تاريخاً سابقاً أو مطابقاً لتاريخ :date.',
    'between'              => [
        'numeric' => 'يجب أن تكون قيمة :attribute بين :min و :max.',
        'file'    => 'يجب أن يكون حجم ملف :attribute بين :min و :max كيلوبايت.',
        'string'  => 'يجب أن يكون طول نص :attribute بين :min و :max حرفاً.',
        'array'   => 'يجب أن يحتوي :attribute على عناصر بين :min و :max.',
    ],
    'boolean'              => 'يجب أن تكون قيمة :attribute إما صحيح (true) أو خطأ (false).',
    'confirmed'            => 'تأكيد :attribute غير متطابق.',
    'date'                 => ':attribute ليس تاريخاً صحيحاً.',
    'date_equals'          => 'يجب أن يكون :attribute تاريخاً مطابقاً لـ :date.',
    'date_format'          => ':attribute لا يتطابق مع الصيغة المطلوبة (:format).',
    'different'            => 'يجب أن يكون الحقلان :attribute و :other مختلفين.',
    'digits'               => 'يجب أن يحتوي :attribute على :digits رقم/أرقام.',
    'digits_between'       => 'يجب أن يحتوي :attribute بين :min و :max رقماً.',
    'email'                => 'يجب أن يكون :attribute عنوان بريد إلكتروني صالح.',
    'ends_with'            => 'يجب أن ينتهي :attribute بأحد القيم التالية: :values.',
    'exists'               => 'القيمة المحددة لـ :attribute غير موجودة في النظام.',
    'file'                 => 'يجب أن يكون :attribute ملفاً.',
    'filled'               => 'حقل :attribute مطلوب ولا يمكن تركه فارغاً.',
    'image'                => 'يجب أن يكون :attribute صورة.',
    'in'                   => 'القيمة المختارة لـ :attribute غير صالحة.',
    'in_array'             => 'حقل :attribute غير موجود في :other.',
    'integer'              => 'يجب أن يكون :attribute رقماً صحيحاً.',
    'ip'                   => 'يجب أن يكون :attribute عنوان IP صالحاً.',
    'json'                 => 'يجب أن يكون :attribute نصاً من نوع JSON صالح.',
    'max'                  => [
        'numeric' => 'يجب ألا تكون قيمة :attribute أكبر من :max.',
        'file'    => 'يجب ألا يتجاوز حجم ملف :attribute :max كيلوبايت.',
        'string'  => 'يجب ألا يتجاوز طول نص :attribute :max حرفاً.',
        'array'   => 'يجب ألا يحتوي :attribute على أكثر من :max عناصر.',
    ],
    'min'                  => [
        'numeric' => 'يجب أن تكون قيمة :attribute على الأقل :min.',
        'file'    => 'يجب أن يكون حجم ملف :attribute على الأقل :min كيلوبايت.',
        'string'  => 'يجب ألا يقل طول نص :attribute عن :min أحرف.',
        'array'   => 'يجب أن يحتوي :attribute على الأقل على :min عناصر.',
    ],
    'not_in'               => 'قيمة :attribute محجوزة للنظام ولا يمكن استخدامها.',
    'numeric'              => 'يجب أن يكون :attribute رقماً.',
    'present'              => 'يجب تقديم حقل :attribute.',
    'regex'                => 'صيغة :attribute غير صالحة.',
    'required'             => 'حقل :attribute مطلوب ولا يمكن تركه فارغاً.',
    'required_if'          => 'حقل :attribute مطلوب عندما يكون :other هو :value.',
    'required_unless'      => 'حقل :attribute مطلوب ما لم يكن :other ضمن :values.',
    'required_with'        => 'حقل :attribute مطلوب عند تزويد :values.',
    'required_with_all'    => 'حقل :attribute مطلوب عند تزويد كافة الحقول: :values.',
    'required_without'     => 'حقل :attribute مطلوب في حال عدم توفر :values.',
    'required_without_all' => 'حقل :attribute مطلوب عندما لا يتوفر أي من :values.',
    'string'               => 'يجب أن يكون :attribute نصاً.',
    'timezone'             => 'يجب أن يكون :attribute منطقة زمنية صالحة.',
    'unique'               => 'قيمة :attribute مستخدمة بالفعل، يرجى اختيار قيمة أخرى.',
    'url'                  => 'صيغة رابط :attribute غير صحيحة.',
    'uuid'                 => 'يجب أن يكون :attribute معرف UUID صالحاً.',

    /*
    |--------------------------------------------------------------------------
    | أسماء الحقول المخصصة بالعربية (Custom Validation Attributes)
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'name'                         => 'اسم المدرسة',
        'code'                         => 'كود المدرسة',
        'subdomain'                    => 'الرابط الفرعي للمدرسة',
        'email'                        => 'البريد الإلكتروني',
        'phone'                        => 'رقم الهاتف',
        'address'                      => 'العنوان',
        'logo_url'               
              => 'شعار المدرسة',
        'status'                       => 'حالة المدرسة',
        'settings'                     => 'إعدادات المدرسة',
        'settings.subscription_plan'   => 'باقة الاشتراك',
        'settings.subscription_start_date' => 'تاريخ بداية الاشتراك',
        'settings.subscription_end_date'   => 'تاريخ انتهاء الاشتراك',
        'settings.primary_color'       => 'اللون الأساسي',
        'settings.secondary_color'     => 'اللون الثانوي',
        'settings.theme_mode'          => 'نمط العرض',
        'settings.favicon_url'         => 'أيقونة المتصفح',
        'settings.timezone'            => 'المنطقة الزمنية',
        'settings.school_start_time'   => 'موعد بداية الدوام',
        'settings.school_end_time'     => 'موعد نهاية الدوام',
        'settings.weekend_days'        => 'أيام العطلة الأسبوعية',
        'settings.extra_config'        => 'الإعدادات الإضافية',
        'manager'                      => 'بيانات المدير',
        'manager.full_name'            => 'اسم مدير المدرسة',
        'manager.phone_number'         => 'هاتف مدير المدرسة',
        'manager.email'                => 'بريد مدير المدرسة',
        'manager.password'             => 'كلمة مرور مدير المدرسة',
        'identifier'                   => 'اسم المستخدم أو البريد أو رقم الهاتف',
        'password'                     => 'كلمة المرور',
    ],

];
