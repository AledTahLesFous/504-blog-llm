<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Récupérer la clé API depuis l'en-tête
        $providedKey = $request->header('X-API-KEY');

        // Clé API valide dans config/services.php
        $validKey = config('services.api.key');

        // Vérifier la clé
        if (!$providedKey || $providedKey !== $validKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: API key missing or invalid'
            ], 401);
        }

        // Clé correcte → continuer la requête
        return $next($request);
    }
}
