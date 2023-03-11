<?php


return [

    'resource'=>[
        'single' => 'مهمة نظام مجدولة',
        'plural' => 'مهام النظام المجدولة',
        'navigation' => 'الإعدادات',
        'history' => 'عرض سجل التشغيل',
    ],

    'fields' => [
        'command' => 'الامر',
        'arguments' => 'المتغيرات',
        'options' => 'الخيارات',
        'options_with_value' => 'خيارات مع قيمة',
        'expression' => 'تعبير التكرار',
        'log_filename' => 'اسم ملف السجل',
        'output' => 'انتاج |',
        'even_in_maintenance_mode' => 'حتى في وضع الصيانة',
        'without_overlapping' => 'بدون تداخل',
        'on_one_server' => 'تنفيذ الجدولة على خادم واحد فقط',
        'webhook_before' => 'طلب URL قبل',
        'webhook_after' => 'طلب URL بعد',
        'email_output' => 'البريد الإلكتروني لإرسال النتائج',
        'sendmail_success' => 'إرسال بريد إلكتروني في حالة نجاح تنفيذ الأمر',
        'sendmail_error' => 'إرسال بريد إلكتروني في حالة عدم تنفيذ الأمر',
        'log_success' => 'حفظ الأمر في جدول المحفوظات في حالة نجاح تنفيذ الأمر',
        'log_error' => 'حفظ الأمر في جدول المحفوظات في حالة الفشل في تنفيذ الأمر',
        'status' => 'الحالة',
        'data-type' => 'نوع البيانات',
        'run_in_background' => 'تشغيل في الخلفية',
        'created_at' => 'أنشئت في',
        'updated_at' => 'تم التحديث في',
        'never' => 'لم تشتغل بعد',
        'environments' => 'البيئات',
    ],
    'messages' => [
        'no-records-found' => 'لا توجد سجلات.',
        'save-success' => 'تم حفظ المعلومات بنجاح.',
        'save-error' => 'خطأ في حفظ البيانات.',
        'timezone' => 'سيتم تنفيذ جميع الجداول حسب المنطقة الزمنية:',
        'select' => 'حدد أمرًا',
        'custom' => 'أمر مخصص',
        'custom-command-here' => 'أمر مخصص هنا (على سبيل المثال ، `cat / proc / cpuinfo` أو` الحرفي ديسيبل :migrate`)',
        'help-cron-expression' => 'إذا لزم الأمر ، انقر هنا واستخدم أداة لتسهيل إنشاء تعبير المهمة',
        'help-log-filename' => 'إذا تم تعيين ملف السجل ، فستتم كتابة رسائل السجل من هذا المهمة في التخزين / السجلات / <log filename> .log',
        'attention-type-function' => 'تنبيه: يتم تنفيذ معلمات من النوع "function" قبل تنفيذ الجدولة ويتم تمرير رجوعها كمعامل. استخدم بعناية ، يمكن أن يكسر وظيفتك',

    ],
    'status' => [
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'trashed' => 'محذوف',
    ],
    'buttons' => [
        'inactivate' => 'تعطيل',
        'activate' => 'تفعيل',
        'history' => 'سجل الشتغيل',

    ],
    'validation' => [
        'cron' => 'يجب ملء الحقل بتنسيق تعبير التكرار.',
        'regex' => __('validation.alpha_dash') . ' ' . 'الفاصلة مسموح بها أيضًا.'
    ],


];
