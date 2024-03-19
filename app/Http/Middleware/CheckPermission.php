<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HasPermission
{
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        $role_id = session('user_role') ?? '';

        if ($role_id) {
            $allowedRoutes = $this->getAllowedRoutesForRole($role_id);

            $currentRoute = $request->route()->getName();
            if (in_array($currentRoute, $allowedRoutes)) {
                return $next($request);
            }

            return abort(401);
        } else {
            return redirect('/login');
        }
    }

    private function getAllowedRoutesForRole($role_id)
    {
        $roles = [
            'admin' => ['admin.dashboard', 'admin.users', 'admin.settings'],
            'editor' => ['editor.dashboard', 'editor.articles', 'editor.settings'],
        ];

        return $roles[$role_id] ?? [];
    }
}
