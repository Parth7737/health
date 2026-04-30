<?php

namespace App\Providers;

use App\Models\HospitalRolePermissionOverride;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();

        Gate::before(function ($user, string $ability) {
            if (!$user || !$user->hospital_id) {
                return null;
            }

            static $permissionOverrides = [];

            $cacheKey = $user->id . '|' . $user->hospital_id;
            if (!isset($permissionOverrides[$cacheKey])) {
                $roleIds = $user->roles->pluck('id')->all();

                if (empty($roleIds)) {
                    $permissionOverrides[$cacheKey] = [
                        'denied' => [],
                        'allowed' => [],
                    ];
                } else {
                    $overrides = HospitalRolePermissionOverride::query()
                        ->where('hospital_id', $user->hospital_id)
                        ->whereIn('role_id', $roleIds)
                        ->get(['permission_name', 'is_allowed']);

                    $permissionOverrides[$cacheKey] = [
                        'denied' => $overrides
                            ->where('is_allowed', false)
                            ->pluck('permission_name')
                            ->unique()
                            ->values()
                            ->all(),
                        'allowed' => $overrides
                            ->where('is_allowed', true)
                            ->pluck('permission_name')
                            ->unique()
                            ->values()
                            ->all(),
                    ];
                }
            }

            if (in_array($ability, $permissionOverrides[$cacheKey]['denied'], true)) {
                return false;
            }

            if (in_array($ability, $permissionOverrides[$cacheKey]['allowed'], true)) {
                return true;
            }

            return null;
        });
    }
}
