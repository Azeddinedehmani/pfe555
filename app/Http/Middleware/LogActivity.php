<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    public function terminate(Request $request, Response $response): void
    {
        if ($request->user()) {
            // Journaliser uniquement certaines routes (exemple)
            $loggableRoutes = [
                'products.store', 
                'products.update', 
                'products.destroy',
                'sales.store',
                'sales.update',
                'sales.destroy',
                'clients.store',
                'clients.update',
                'clients.destroy',
                'users.store',
                'users.update',
                'users.destroy',
                'prescriptions.store',
                'prescriptions.update',
                'prescriptions.destroy',
            ];
            
            if (in_array($request->route()->getName(), $loggableRoutes)) {
                $action = explode('.', $request->route()->getName())[1];
                $model = explode('.', $request->route()->getName())[0];
                
                activity_log(
                    $action,
                    null,
                    "Accès à {$model}.{$action} - " . ($response->isSuccessful() ? 'Succès' : 'Échec')
                );
            }
        }
    }
}