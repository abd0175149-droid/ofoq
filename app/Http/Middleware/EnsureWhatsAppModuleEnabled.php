<?php

namespace App\Http\Middleware;

use App\Support\WhatsAppModule;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * الوحدة مطفأة (WhatsAppModule): كل مساراتها 404 كأنها غير موجودة، بما فيها الويبهوك والواجهات البرمجية.
 */
class EnsureWhatsAppModuleEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_if(! WhatsAppModule::enabled() && WhatsAppModule::covers($request), 404);

        return $next($request);
    }
}
