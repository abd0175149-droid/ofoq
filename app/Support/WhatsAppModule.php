<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * وحدة الواتساب كاملة (الصندوق والبوت وطلبات التسعير والإرسال والمشاركة والويبهوك) خلف مفتاح واحد.
 * WHATSAPP_ENABLED=false (الافتراضي في أفق): لا تظهر في القوائم، ومساراتها 404، ومهامها المجدولة لا تعمل.
 * الكود والجداول باقية كما هي، فالتفعيل لاحقاً تغيير قيمة واحدة ثم up -d --force-recreate.
 */
final class WhatsAppModule
{
    /** مسارات الوحدة (أنماط Request::is). */
    public const PATHS = [
        'whatsapp', 'whatsapp/*',
        'api/whatsapp', 'api/whatsapp/*',
        'quote-requests', 'quote-requests/*',
        'api/invoices/*/whatsapp-targets', 'api/invoices/*/send-whatsapp',
        'api/offers/*/share', 'offers/*/qr', 'offers/*/qr/*', 'offers/*/card-qr', 'offers/*/story', 'offers/*/story/*',
    ];

    /** مجموعات الصلاحيات التي لا معنى لها بلا الوحدة (تُخفى من شاشة الأدوار). */
    public const PERMISSION_GROUPS = ['whatsapp', 'quotes'];

    public static function enabled(): bool
    {
        return (bool) config('whatsapp.enabled', false);
    }

    public static function covers(Request $request): bool
    {
        return $request->is(...self::PATHS);
    }

    /** أمر مجدول من أوامر الوحدة: whatsapp:* وops:tick (لا يشغّل إلا أمرين منها، OpsTick::COMMANDS). */
    public static function ownsCommand(?string $command): bool
    {
        return is_string($command) && preg_match('/\bartisan["\']?\s+["\']?(whatsapp:|ops:tick\b)/', $command) === 1;
    }
}
