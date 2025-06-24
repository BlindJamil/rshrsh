<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $permission The permission name passed from the route definition
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        // Ensure the user is logged in via the 'admin' guard
        if (!Auth::guard('admin')->check()) {
            // Redirect to admin login if not authenticated
            return redirect()->route('admin.login');
        }

        // Get the authenticated admin user
        $admin = Auth::guard('admin')->user();

        // Check if the admin user actually has the required permission
        // This relies on the Admin model having a roles() relationship
        // and the Role model having a hasPermission() method.
        $hasPermission = false;
        if ($admin && $admin->roles) { // Check if admin and roles relationship exists
            foreach ($admin->roles as $role) {
                // Assumes the Role model has a 'hasPermission' method
                if (method_exists($role, 'hasPermission') && $role->hasPermission($permission)) {
                    $hasPermission = true;
                    break; // Exit loop once permission is found
                }
            }
        }


        // If the user doesn't have the permission, abort with 403 Forbidden
        if (!$hasPermission) {
            abort(403, 'Unauthorized action.');
        }

        // User has the permission, proceed with the request
        return $next($request);
    }

} // <-- This is the single, correct closing brace for the Class