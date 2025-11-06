<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class RequestLogger
{
    public function handle($request, Closure $next)
    {
        $start = microtime(true);
        $response = $next($request);
        $duration = number_format((microtime(true) - $start) * 1000, 2);

        Log::info(sprintf(
            '[%s] %s %s %s %sms',
            $request->ip(),
            $request->method(),
            $request->path(),
            $response->status(),
            $duration
        ));

        return $response;
    }
}
