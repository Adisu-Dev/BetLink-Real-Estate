<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        if ($request->user() && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            ActivityLog::create([
                'log_name'    => 'api',
                'description' => $request->method() . ' ' . $request->path(),
                'causer_type' => \App\Models\User::class,
                'causer_id'   => $request->user()->id,
                'properties'  => ['ip' => $request->ip(), 'status' => $response->getStatusCode()],
            ]);
        }
    }
}
