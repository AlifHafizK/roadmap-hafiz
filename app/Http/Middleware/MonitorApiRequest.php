<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class MonitorApiRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        try {
            $response = $next($request);

            $duration = round(
                (microtime(true) - $startTime) * 1000,
                2
            );

            Log::info('API Request Monitoring', [
                'method' => $request->method(),
                'endpoint' => $request->path(),
                'status_code' => $response->getStatusCode(),
                'duration_ms' => $duration,
                'ip' => $request->ip(),
            ]);

            return $response;

        } catch (Throwable $e) {
            $duration = round(
                (microtime(true) - $startTime) * 1000,
                2
            );

            Log::error('API Request Monitoring - Exception', [
                'method' => $request->method(),
                'endpoint' => $request->path(),
                'duration_ms' => $duration,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
