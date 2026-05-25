<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\{ AdminMiddleware, HospitalMiddleware, HospitalUserMiddleware, StateAdminMiddleware};

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
        $middleware->alias([
            'superadmin' => AdminMiddleware::class,
            'state-admin' => StateAdminMiddleware::class,
            'hospital' => HospitalMiddleware::class,
            'hospital.user' => HospitalUserMiddleware::class,
            'scheme_preauth.import' => \App\Http\Middleware\AuthorizeSchemePreauthImport::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'integration/scheme-preauth/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
