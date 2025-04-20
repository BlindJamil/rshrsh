<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $admin = Auth::guard('admin')->user();
        $hasPermission = false;

        foreach ($admin->roles as $role) {
            if ($role->hasPermission($permission)) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
} 