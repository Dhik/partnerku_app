<?php

namespace App\Domain\OtherSpent\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Domain\OtherSpent\BLL\OtherSpent\OtherSpentBLL;
use App\Domain\OtherSpent\BLL\OtherSpent\OtherSpentBLLInterface;
use App\Domain\OtherSpent\DAL\OtherSpent\OtherSpentDAL;
use App\Domain\OtherSpent\DAL\OtherSpent\OtherSpentDALInterface;
use App\Domain\OtherSpent\Policies\OtherSpentPolicy;
use App\Domain\OtherSpent\Models\OtherSpent;

class OtherSpentProvider extends ServiceProvider
{
    protected $namespace = 'App\Domain\OtherSpent\Controllers';

    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        OtherSpentBLLInterface::class => OtherSpentBLL::class,
        OtherSpentDALInterface::class => OtherSpentDAL::class,
    ];

    /** The policy mappings for the domain.
     *
     * @var array
     */
    protected $policies = [
        OtherSpent::class => OtherSpentPolicy::class,
    ];


    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        //
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerEvents();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoutes();
        $this->registerPolicies();
    }

    /**
     * Register the domain's routes.
     *
     * @return void
     */
    public function registerRoutes()
    {
        if (!$this->app->routesAreCached()) {
            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('app/Domain/OtherSpent/Routes/web.php'));

            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('app/Domain/OtherSpent/Routes/api.php'));

            $this->app->booted(function () {
                $this->app['router']->getRoutes()->refreshNameLookups();
                $this->app['router']->getRoutes()->refreshActionLookups();
            });
        }
    }

    /**
     * Register the domain's policies.
     *
     * @return void
     */
    public function registerPolicies()
    {
        foreach ($this->policies as $key => $value) {
            Gate::policy($key, $value);
        }
    }

    public function registerEvents()
    {
        $this->booting(function () {
            foreach ($this->listen as $event => $listeners) {
                foreach (array_unique($listeners) as $listener) {
                    Event::listen($event, $listener);
                }
            }
        });
    }
}
