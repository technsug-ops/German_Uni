<?php

namespace App\Http\Middleware;

use App\Models\MenuPage;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePageEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();

        if (! $routeName) {
            return $next($request);
        }

        if (! MenuPage::isKeyEnabled($routeName) && MenuPage::isKeyProtected($routeName)) {
            abort(404);
        }

        return $next($request);
    }
}
