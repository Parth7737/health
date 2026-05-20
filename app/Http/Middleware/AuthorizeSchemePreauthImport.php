<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeSchemePreauthImport
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! (bool) config('scheme_preauth.import_enabled', true)) {
            return response()->json([
                'status' => false,
                'message' => 'Scheme preauth reference import is disabled.',
            ], 403);
        }

        if ($this->hasValidImportToken($request)) {
            return $next($request);
        }

        $user = $request->user();
        if ($user && ($user->hasRole('Master Admin') || $user->can('manage-roles'))) {
            return $next($request);
        }

        return response()->json([
            'status' => false,
            'message' => 'Unauthorized. Provide a valid X-Scheme-Preauth-Import-Token or sign in as Master Admin.',
        ], 401);
    }

    protected function hasValidImportToken(Request $request): bool
    {
        $expected = trim((string) config('scheme_preauth.import_token'));
        if ($expected === '') {
            return false;
        }

        $provided = trim((string) $request->header('X-Scheme-Preauth-Import-Token', ''));

        return $provided !== '' && hash_equals($expected, $provided);
    }
}
