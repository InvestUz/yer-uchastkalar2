<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user is active
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Сизнинг ҳисобингиз фаол эмас. Администратор билан боғланинг.'
            ]);
        }

        // If no roles specified, just check authentication
        if (empty($roles)) {
            return $next($request);
        }

        // Check if user has required role
        if (!in_array($user->role, $roles)) {
            abort(403, 'Сизнинг бу саҳифага кириш ҳуқуқингиз йўқ.');
        }

        return $next($request);
    }
}
