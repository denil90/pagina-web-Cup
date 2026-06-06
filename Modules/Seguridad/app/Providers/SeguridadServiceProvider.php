<?php

namespace Modules\Seguridad\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class SeguridadServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Seguridad';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'seguridad';

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
     * Define module schedules.
     * 
     * @param $schedule
     */
    /**
     * Boot the service provider.
     */
    public function boot(): void
    {
        parent::boot();
        $this->app['router']->aliasMiddleware('verificar.rol', \Modules\Seguridad\Http\Middleware\VerificarRol::class);
    }
}
