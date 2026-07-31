<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\AuditService;
use Illuminate\Support\Facades\Route;

class AuditMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip audit logging for diagnostic routes
        if ($request->is('check-db')) {
            return $response;
        }

        $method = $request->method();
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            try {
                $routeName = Route::currentRouteName() ?? $request->path();
                $action = 'Route: ' . $routeName . ' Method: ' . $method;
                
                $recordType = null;
                $recordId = null;
                
                $params = Route::current()?->parameters();
                if (!empty($params)) {
                    $firstParamKey = array_key_first($params);
                    $firstParamVal = $params[$firstParamKey];
                    $recordType = ucfirst($firstParamKey);
                    $recordId = is_numeric($firstParamVal) ? (int)$firstParamVal : (is_object($firstParamVal) ? $firstParamVal->id ?? null : null);
                }

                AuditService::logStatic(
                    $action,
                    $recordType,
                    $recordId,
                    null,
                    json_encode($request->except(['password', 'password_confirmation', '_token', '_method']))
                );
            } catch (\Throwable $e) {
                // Ignore DB logging exceptions so main request never crashes
            }
        }

        return $response;
    }
}
