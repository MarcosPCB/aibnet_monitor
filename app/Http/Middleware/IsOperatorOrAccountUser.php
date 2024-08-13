<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsOperatorOrAccountUser
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Verifica se o usuário é um Operator
        if ($user && $user instanceof \App\Models\Operator) {
            return $next($request);
        }

        // Verifica se o usuário pertence à Account específica
        if ($user && $user->account_id == $request->route('account_id')) {
            return $next($request);
        }

        return response()->json(['message' => 'Access denied.'], 403);
    }
}
