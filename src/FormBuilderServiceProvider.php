<?php

namespace LaurentMeuwly\FormBuilder;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use LaurentMeuwly\FormBuilder\Renderers\NullRenderer;
use LaurentMeuwly\FormBuilder\Contracts\RendersForm;


class FormBuilderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/formbuilder.php', 'formbuilder');

        $this->app->singleton(RendersForm::class, function ($app) {
            $class = config('formbuilder.renderer', NullRenderer::class);
            return $app->make($class);
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/formbuilder.php' => config_path('formbuilder.php'),
        ], 'formbuilder-config');

        $this->publishes([
            __DIR__.'/../database/migrations/create_formbuilder_tables.php.stub' => $this->getMigrationFileName('create_formbuilder_tables.php'),
        ], 'formbuilder-migrations');

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'mp');
        //$this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     */
    protected function getMigrationFileName(string $migrationFileName): string
    {
        $timestamp = date('Y_m_d_His');

        $filesystem = $this->app->make(Filesystem::class);

        return Collection::make([$this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR])
            ->flatMap(fn ($path) => $filesystem->glob($path.'*_'.$migrationFileName))
            ->push($this->app->databasePath()."/migrations/{$timestamp}_{$migrationFileName}")
            ->first();
    }
}
