<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

// dd($request->path());

        if($request->is('api/login') || $request->is('api/register')) {
          return  $next($request);
        } else if( $request->is('api/*')  && !Auth::guard('sanctum')->check()) {
             return response()->json([
                'status' => false,
                'message' => 'Unauthorized access. Please login first!'
            ], 401);

        }



        return $next($request);
    }
}
