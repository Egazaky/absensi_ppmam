<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('admin', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('pengurus', function (User $user) {
            return $user->hasAnyRole([User::ROLE_PENGURUS, User::ROLE_SUPER_ADMIN]);
        });

        Gate::define('superadmin', function (User $user) {
            return $user->isSuperAdmin();
        });

        Gate::define('staff', function (User $user) {
            return $user->isStaff();
        });
    }
}
