<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class language
{

    public function handle(Request $request, Closure $next)
    {
        if (Session()->has('applocale') and array_key_exists(Session()->get('applocale'), config('languages'))) {
            App::setlocale(Session()->get('applocale'));
        } else {
            App::setlocale(config('app.fallback_locale'));
        }
        return $next($request);
    }
}
