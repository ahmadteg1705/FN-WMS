<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        $user = $request->user();

        abort_unless($user, 401);

        $currentRole = strtolower(trim((string) ($user->role ?? '')));

        $allowedRoles = collect($roles)
            ->map(fn (string $role) => strtolower(trim($role)))
            ->filter()
            ->values();

        abort_unless(
            $allowedRoles->contains($currentRole),
            403,
            'Anda tidak memiliki akses ke Modul Aktivasi.'
        );

        return $next($request);
    }
}
