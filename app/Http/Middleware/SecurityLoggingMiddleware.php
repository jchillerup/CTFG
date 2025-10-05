<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SecurityLoggingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        $response = $next($request);
        
        $this->logSecurityEvent($request, $response, $startTime);
        
        return $response;
    }
    
    /**
     * Log security-related events
     */
    private function logSecurityEvent(Request $request, Response $response, float $startTime): void
    {
        $executionTime = microtime(true) - $startTime;
        
        // Log suspicious activities
        if ($this->isSuspiciousRequest($request, $response)) {
            Log::channel('security')->warning('Suspicious request detected', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'status_code' => $response->getStatusCode(),
                'execution_time' => $executionTime,
                'headers' => $request->headers->all(),
                'input' => $this->sanitizeInput($request->all()),
                'timestamp' => now()->toISOString(),
            ]);
        }
        
        // Log failed authentication attempts
        if ($response->getStatusCode() === 401) {
            Log::channel('security')->info('Authentication failed', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'timestamp' => now()->toISOString(),
            ]);
        }
        
        // Log rate limit violations
        if ($response->getStatusCode() === 429) {
            Log::channel('security')->warning('Rate limit exceeded', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'timestamp' => now()->toISOString(),
            ]);
        }
        
        // Log file uploads
        if ($request->hasFile('file')) {
            Log::channel('security')->info('File upload attempt', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'file_name' => $request->file('file')->getClientOriginalName(),
                'file_size' => $request->file('file')->getSize(),
                'file_type' => $request->file('file')->getMimeType(),
                'timestamp' => now()->toISOString(),
            ]);
        }
    }
    
    /**
     * Check if request is suspicious
     */
    private function isSuspiciousRequest(Request $request, Response $response): bool
    {
        // Check for SQL injection patterns
        $sqlPatterns = [
            '/union\s+select/i',
            '/drop\s+table/i',
            '/delete\s+from/i',
            '/insert\s+into/i',
            '/update\s+set/i',
            '/or\s+1\s*=\s*1/i',
            '/and\s+1\s*=\s*1/i',
        ];
        
        $input = json_encode($request->all());
        foreach ($sqlPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        // Check for XSS patterns
        $xssPatterns = [
            '/<script/i',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload=/i',
            '/onerror=/i',
            '/onclick=/i',
            '/onmouseover=/i',
        ];
        
        foreach ($xssPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        // Check for path traversal
        $pathTraversalPatterns = [
            '/\.\.\//',
            '/\.\.\\\\/',
            '/%2e%2e%2f/',
            '/%2e%2e%5c/',
        ];
        
        foreach ($pathTraversalPatterns as $pattern) {
            if (preg_match($pattern, $request->fullUrl())) {
                return true;
            }
        }
        
        // Check for suspicious user agents
        $suspiciousUserAgents = [
            '/sqlmap/i',
            '/nikto/i',
            '/nmap/i',
            '/masscan/i',
            '/zap/i',
            '/burp/i',
            '/wget/i',
            '/curl/i',
        ];
        
        $userAgent = $request->userAgent();
        foreach ($suspiciousUserAgents as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return true;
            }
        }
        
        // Check for high response times (potential DoS)
        if ($response->getStatusCode() >= 500) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Sanitize input for logging (remove sensitive data)
     */
    private function sanitizeInput(array $input): array
    {
        $sensitiveFields = ['password', 'token', 'secret', 'key', 'api_key', 'auth'];
        
        foreach ($input as $key => $value) {
            if (in_array(strtolower($key), $sensitiveFields)) {
                $input[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $input[$key] = $this->sanitizeInput($value);
            }
        }
        
        return $input;
    }
}