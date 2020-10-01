<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Models' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes();
        Passport::tokensCan([
            'vital-admin' => 'Edit everything',
            'vital-manager' => 'Edit almost everything',
            'general-manager' => 'Manage everything about own company',
            'manager' => 'Manage almost everything about own company',
            'user' => 'User scope',
            'customer' => 'customer scope',
        ]);
    }
}
