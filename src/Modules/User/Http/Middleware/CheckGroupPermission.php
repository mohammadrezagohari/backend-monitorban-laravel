<?php

namespace Modules\User\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckGroupPermission
{
    public function handle($request, Closure $next, $permission)
    {
        $user = Auth::user();

        if (
            method_exists($user, 'hasPermissionViaGroups') &&
            $user->hasPermissionViaGroups($permission)
        ) {
            return $next($request);
        }

        // فالو‌بک به hasPermissionTo معمولی Spatie
        if ($user->can($permission)) {
            return $next($request);
        }

        abort(403, 'You do not have permission.');
    }
}

