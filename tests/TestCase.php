<?php

declare(strict_types=1);

namespace LaurentMeuwly\FormBuilder\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use LaurentMeuwly\FormBuilder\FormBuilderServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [FormBuilderServiceProvider::class];
    }

    protected function defineEnvironment($app)
    {
        // DB sqlite in-memory
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Table names configurables (doivent correspondre à ta config)
        $app['config']->set('formbuilder.table_names', [
            'forms'          => 'fb_forms',
            'form_items'     => 'fb_form_items',
            'branching_rules'=> 'fb_branching_rules',
            'answer_sets'    => 'fb_answer_sets',
            'answers'        => 'fb_answers',
        ]);

        // Renderer par défaut (n’impose pas Filament)
        $app['config']->set('formbuilder.renderer', \LaurentMeuwly\FormBuilder\Renderers\NullRenderer::class);
    }

    protected function defineDatabaseMigrations()
    {
        // Charge les migrations du package (chemin réel du package)
        //$this->loadMigrationsFrom(realpath(__DIR__.'/../..').'/database/migrations');
        $this->loadMigrationsFrom(__DIR__.'/migrations');

        // Table factice pour une entité métier (utilisée par le Feature test)
        Schema::create('test_participants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }
}
