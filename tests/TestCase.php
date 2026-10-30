<?php

namespace LaurentMeuwly\FormBuilder\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use LaurentMeuwly\FormBuilder\FormBuilderServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [FormBuilderServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
    }

    protected function defineDatabaseMigrations(): void
    {
        // Run the real package migrations, in their publication order.
        foreach ([
            'create_formbuilder_tables.php.stub',
            'add_is_required_to_form_items_table.php.stub',
        ] as $stub) {
            $migration = include __DIR__.'/../database/migrations/'.$stub;
            $migration->up();
        }

        // Host-application model answering forms (see Fixtures\Participant).
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }
}
