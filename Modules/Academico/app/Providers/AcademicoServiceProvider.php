<?php

namespace Modules\Academico\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Modules\Academico\Contracts\CareerQueryInterface;
use Modules\Academico\Contracts\PeriodQueryInterface;
use Modules\Academico\Contracts\SubjectQueryInterface;
use Modules\Academico\Services\EloquentCareerQuery;
use Modules\Academico\Services\EloquentPeriodQuery;
use Modules\Academico\Services\EloquentSubjectQuery;

class AcademicoServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Academico';
    protected string $nameLower = 'academico';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    /**
     * Registra los bindings de interfaces → implementaciones.
     * Otros módulos inyectan las interfaces en sus constructores.
     */
    public function register(): void
    {
        parent::register();

        $this->app->bind(CareerQueryInterface::class, EloquentCareerQuery::class);
        $this->app->bind(PeriodQueryInterface::class, EloquentPeriodQuery::class);
        $this->app->bind(SubjectQueryInterface::class, EloquentSubjectQuery::class);
    }
}
