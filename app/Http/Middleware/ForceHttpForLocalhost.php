<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttpForLocalhost
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $isLocalhost = in_array($host, ['localhost', '127.0.0.1']) || 
                      strpos($host, '127.0.0.1') !== false || 
                      strpos($host, 'localhost') !== false;
        
        if ($isLocalhost) {
            // Force HTTP scheme for localhost
            \URL::forceScheme('http');
            $request->server->set('HTTPS', 'off');
            $request->server->set('SERVER_PORT', $request->getPort() ?: '8000');
            $request->server->set('HTTP_HOST', $host);
        }
        
        return $next($request);
    }
}




