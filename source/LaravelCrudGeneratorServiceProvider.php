<?php

namespace SakshiChavan\LaravelCrudGenerator;

use Illuminate\Support\ServiceProvider;
use SakshiChavan\LaravelCrudGenerator\Commands\GenerateCrud;
use SakshiChavan\LaravelCrudGenerator\Commands\MakeCrudTemplate;
use SakshiChavan\LaravelCrudGenerator\Services\GenerateCrudFilesService;

class LaravelCrudGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Register any package services here
        $this->app->singleton(GenerateCrudFilesService::class, function ($app) {
            return new GenerateCrudFilesService($app['console']);
        });

        // Optionally, you can bind other classes or functionality
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish stubs to the application's resources
        $this->publishes([
            __DIR__ . '/../resources/stubs' => resource_path('crud-generator-stubs'),
        ], 'crud-generator-stubs');


        // Register commands
        $this->commands([
            MakeCrudTemplate::class,
            GenerateCrud::class,
        ]);
    }
}
