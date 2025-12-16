<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckNotTamu
{
    /**
     * Handle an incoming request.
     * Blok akses untuk role Tamu
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Jika role adalah Tamu, redirect ke dashboard dengan error
        if (auth()->user()->isTamu()) {
            return redirect()->route('dashboard')
                ->with('error', 'Akses ditolak. Anda hanya dapat mengakses Dashboard dan Download Data.');
        }

        return $next($request);
    }
}