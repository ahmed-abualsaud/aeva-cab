<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\CustomException;
use Illuminate\Contracts\Session\Session;
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
            $request->session()->forget('locale');
            $session = $request->getSession();

            if (!$session->has('locale')) {
                $session->put('locale', $request->getPreferredLanguage(self::LOCALES));
            }

            if ($request->has('lang')) {
                $lang = $request->get('lang');
                if (in_array($lang, self::LOCALES)) {
                    $session->put('locale', $lang);
                }
            }
            app()->setLocale($session->get('locale'));

            return $next($request);

        } catch (Exception $e) {
            throw new CustomException('Could not configure App language!');
        }
    }
}
