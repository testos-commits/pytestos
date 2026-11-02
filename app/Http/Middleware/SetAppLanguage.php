<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetAppLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('sanctum')->user()) {
            app()->setLocale(auth('sanctum')->user()->app_language);
        } else {
            if(request()->has('lang')) {
                if(in_array(request()->lang, config('app.supported_locales'))) {
                    app()->setLocale(request()->lang);
                }
            } else {
                app()->setLocale(config('app.fallback_locale'));
            }
        }

        return $next($request);
    }
}
