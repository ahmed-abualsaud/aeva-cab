<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Exceptions\CustomException;

class UnblockedUsers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth('user')->isBlocked()) {
            throw new CustomException(__('lang.your_account_is_disabled'));
        }
      
        return $next($request);
    }
}
