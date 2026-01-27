<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuditLogMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log for authenticated users
        if (Auth::check() && $this->shouldLog($request)) {
            $this->logRequest($request, $response);
        }

        return $response;
    }

    /**
     * Determine if the request should be logged
     */
    protected function shouldLog(Request $request): bool
    {
        // Skip logging for certain routes
        $excludedPaths = [
            'api/*',
            '*.js',
            '*.css',
            '*.map',
            'favicon.ico',
            '_debugbar/*',
            'livewire/*',
        ];

        foreach ($excludedPaths as $pattern) {
            if ($request->is($pattern)) {
                return false;
            }
        }

        // Only log GET requests for viewing, and write operations
        return $request->isMethod('GET') || 
               in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']);
    }

    /**
     * Log the request
     */
    protected function logRequest(Request $request, Response $response): void
    {
        try {
            $action = $this->determineAction($request);
            $module = $this->determineModule($request);

            // Only log if we can determine module and action
            if (!$module || !$action) {
                return;
            }

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'module' => $module,
                'entity_type' => null,
                'entity_id' => $this->extractEntityId($request),
                'status_code' => $response->getStatusCode(),
                'old_values' => null,
                'new_values' => $request->method() !== 'GET' ? json_encode($request->except(['password', '_token', '_method'])) : null,
                'sensitive_data_accessed' => $this->hasSensitiveData($request),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => session()->getId(),
            ]);
        } catch (\Exception $e) {
            // Silent fail
            \Log::error('Audit log middleware failed: ' . $e->getMessage());
        }
    }

    /**
     * Determine action from request
     */
    protected function determineAction(Request $request): ?string
    {
        $method = $request->method();
        $path = $request->path();

        if ($method === 'GET') {
            if (str_contains($path, '/edit')) return 'viewed_edit_form';
            if (str_contains($path, '/create')) return 'viewed_create_form';
            if (preg_match('/\/\d+$/', $path)) return 'viewed';
            return 'listed';
        }

        return match($method) {
            'POST' => 'created',
            'PUT', 'PATCH' => 'updated',
            'DELETE' => 'deleted',
            default => null
        };
    }

    /**
     * Determine module from request path
     */
    protected function determineModule(Request $request): ?string
    {
        $path = $request->path();
        
        // Extract module from path
        if (preg_match('/\/(assessments?|users?|companies|design-factors?|gamo|evidence|admin)/', $path, $matches)) {
            return ucfirst(rtrim($matches[1], 's'));
        }

        return null;
    }

    /**
     * Extract entity ID from request
     */
    protected function extractEntityId(Request $request): ?int
    {
        // Try to extract ID from URL
        if (preg_match('/\/(\d+)(?:\/|$)/', $request->path(), $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    /**
     * Check if request contains sensitive data
     */
    protected function hasSensitiveData(Request $request): bool
    {
        $sensitiveFields = ['password', 'password_confirmation', 'api_token', 'secret', 'token'];
        
        foreach ($sensitiveFields as $field) {
            if ($request->has($field)) {
                return true;
            }
        }

        return false;
    }
}
