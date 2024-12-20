<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Models\Admin;
class EnsureUserIsCorrectType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $type): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        
        if ($type === 'user' && ! $user instanceof User) {
            return redirect('/login');
        }

        if ($type === 'admin' && ! $user instanceof Admin) {
            return redirect('/login');
        }
    
        if ($type === 'support' && ! $user instanceof Support) {
            return redirect('/login');
        }

        return $next($request);
    }
}
