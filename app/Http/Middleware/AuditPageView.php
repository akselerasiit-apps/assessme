<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuditPageView
{
    /**
     * Handle an incoming request and log page views
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log successful GET requests for authenticated users
        if (
            $request->isMethod('GET') &&
            Auth::check() &&
            $response->getStatusCode() === 200 &&
            !$request->ajax() &&
            !$this->shouldSkipLogging($request)
        ) {
            try {
                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'viewed',
                    'module' => $this->getModuleFromUrl($request->path()),
                    'entity_type' => null,
                    'entity_id' => $this->getEntityIdFromUrl($request->path()),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'session_id' => session()->getId(),
                ]);
            } catch (\Exception $e) {
                \Log::error('Page view audit log failed: ' . $e->getMessage());
            }
        }

        return $response;
    }

    /**
     * Determine if this request should skip logging
     */
    private function shouldSkipLogging(Request $request): bool
    {
        $skipPaths = [
            'js/',
            'css/',
            'images/',
            'favicon',
            'storage/',
            'api/',
            '.well-known/',
            'livewire/',
        ];

        $path = $request->path();

        foreach ($skipPaths as $skipPath) {
            if (str_starts_with($path, $skipPath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract module name from URL path
     */
    private function getModuleFromUrl(string $path): string
    {
        $segments = explode('/', $path);
        
        if (count($segments) === 0) {
            return 'Dashboard';
        }

        $firstSegment = $segments[0];

        $moduleMap = [
            'assessments' => 'Assessment',
            'companies' => 'Company',
            'admin' => 'Admin',
            'profile' => 'Profile',
            'dashboard' => 'Dashboard',
            '' => 'Dashboard',
        ];

        return $moduleMap[$firstSegment] ?? ucfirst($firstSegment);
    }

    /**
     * Extract entity ID from URL if present
     */
    private function getEntityIdFromUrl(string $path): ?int
    {
        // Match patterns like /assessments/123 or /companies/456/edit
        if (preg_match('/\/(\d+)(?:\/|$)/', $path, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}
