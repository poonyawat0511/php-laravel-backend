<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsStaff
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response {
        if ($request->user() && $request->user()->role === 'staff') {
            return $next($request);
        }

        return response()->json([
            'status_code' => 403,
            'status'      => 'error',
            'message'     => 'Forbidden: คุณไม่มีสิทธิ์เข้าถึง (เฉพาะ Staff เท่านั้น)',
            'data'        => null,
            'errors'      => null
        ], 403);
    }
}
