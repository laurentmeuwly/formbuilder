<?php

namespace LaurentMeuwly\FormBuilder;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use LaurentMeuwly\FormBuilder\Contracts\RendersForm;
use LaurentMeuwly\FormBuilder\Renderers\NullRenderer;

class FormBuilderServiceProvider extends ServiceProvider
{
    /**
     * Package migrations, in the order they must run.
     *
     * @var list<string>
     */
    protected const MIGRATIONS = [
        'create_formbuilder_tables.php',
        'add_is_required_to_form_items_table.php',
    ];

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/formbuilder.php', 'formbuilder');

        $this->app->singleton(RendersForm::class, function (Application $app): RendersForm {
            $class = config('formbuilder.renderer', NullRenderer::class);

            $renderer = $app->make($class);

            if (! $renderer instanceof RendersForm) {
                throw new InvalidArgumentException(sprintf(
                    'The configured formbuilder renderer [%s] must implement %s.',
                    is_string($class) ? $class : get_debug_type($class),
                    RendersForm::class,
                ));
            }

            return $renderer;
        });
    }

    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/formbuilder.php' => config_path('formbuilder.php'),
        ], 'formbuilder-config');

        $migrations = [];
        $now = time();

        foreach (self::MIGRATIONS as $offset => $migration) {
            $migrations[__DIR__."/../database/migrations/{$migration}.stub"]
                = $this->getMigrationFileName($migration, $now + $offset);
        }

        $this->publishes($migrations, 'formbuilder-migrations');
    }

    /**
     * Return the already published migration if it exists, otherwise a new
     * path timestamped with $timestamp. Each package migration gets its own
     * second so that they always run in the intended order.
     */
    protected function getMigrationFileName(string $migrationFileName, int $timestamp): string
    {
        $directory = $this->app->databasePath('migrations');

        $existing = $this->app->make(Filesystem::class)
            ->glob($directory.DIRECTORY_SEPARATOR.'*_'.$migrationFileName);

        return $existing[0] ?? $directory.DIRECTORY_SEPARATOR.date('Y_m_d_His', $timestamp).'_'.$migrationFileName;
    }
}
