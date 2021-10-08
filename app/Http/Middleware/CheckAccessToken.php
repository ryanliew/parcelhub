<?php

namespace App\Http\Middleware;

use Closure;

class CheckAccessToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $access_token = session()->get('access_token');  
        if($access_token)
            return $next($request);
        else {
            return redirect('logout');
        }
    }
}
