<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\CustomException;
use Illuminate\Http\Request;
class ConfigureAppLanguage
{
    const LOCALES = ['en', 'ar'];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {   
        try {
            
            $locale = $request->getPreferredLanguage(self::LOCALES);
            app()->setLocale($locale);

            return $next($request);

        } catch (Exception $e) {
            throw new CustomException(__('lang.SetLanguageFailed'));
        }
    }
}
