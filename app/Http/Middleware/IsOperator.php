<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsOperator
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Verifica se o usuário é um Operator
        if ($user && $user instanceof \App\Models\Operator) {
            return $next($request);
        }

        return response()->json(['message' => 'Access denied.'], 403);
    }
}
