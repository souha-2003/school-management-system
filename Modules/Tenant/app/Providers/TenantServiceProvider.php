<?php

namespace Modules\Tenant\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class TenantServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Tenant';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'tenant';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    // protected array $commands = [];

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    /**
     * Register module services.
     */
    public function register(): void
    {
        parent::register();

        $this->app->singleton(\Modules\Tenant\Services\TenantContext::class, function () {
            return new \Modules\Tenant\Services\TenantContext();
        });
    }
}

