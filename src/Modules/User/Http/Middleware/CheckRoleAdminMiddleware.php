<?php

namespace Modules\User\Http\Middleware;

use Closure;
use DB;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HTTPResponse;
class CheckRoleAdminMiddleware
{

    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user || !$user->hasRole($roles)) {
            return abort(code: HTTPResponse::HTTP_FORBIDDEN, message: "شما دسترسی به این بخش را ندارید");
        }

        return $next($request);
    }
}
