<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Lenguaje
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check header request and determine localizaton
        $local = ($request->hasHeader('culture')) ? $request->header('culture') : 'es';
        // set laravel localization
        if (strpos($local, 'pt') !== false) {
            $local = "pt";
        } elseif (strpos($local, 'en') !== false) {
            $local = "en";
        } else {
            $local = "es";
        }
        app()->setLocale($local);
        // continue request
        return $next($request);
    }
}
