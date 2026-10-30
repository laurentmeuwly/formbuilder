<?php

use Illuminate\Support\Facades\File;
use LaurentMeuwly\FormBuilder\Contracts\RendersForm;
use LaurentMeuwly\FormBuilder\Enums\FormFieldType;
use LaurentMeuwly\FormBuilder\Facades\FormBuilder;
use LaurentMeuwly\FormBuilder\Models\Form;
use LaurentMeuwly\FormBuilder\Renderers\NullRenderer;
use LaurentMeuwly\FormBuilder\Tests\Fixtures\InvalidRenderer;

it('binds the configured renderer', function () {
    expect(app(RendersForm::class))->toBeInstanceOf(NullRenderer::class);
});

it('rejects a renderer that does not implement RendersForm', function () {
    config()->set('formbuilder.renderer', InvalidRenderer::class);
    app()->forgetInstance(RendersForm::class);

    app(RendersForm::class);
})->throws(InvalidArgumentException::class);

it('renders a form through the facade', function () {
    $form = Form::create(['title' => 'Form']);
    $form->items()->createMany([
        ['key' => 'q1', 'type' => FormFieldType::TEXT],
        ['key' => 'q2', 'type' => FormFieldType::NUMBER],
    ]);

    $output = FormBuilder::render($form->fresh());

    expect($output)->toHaveKeys(['form', 'items'])
        ->and($output['form']['title'])->toBe('Form')
        ->and($output['items'])->toHaveCount(2);
});

it('publishes the migrations in the order they must run', function () {
    $directory = database_path('migrations');
    File::ensureDirectoryExists($directory);
    File::cleanDirectory($directory);

    $this->artisan('vendor:publish', ['--tag' => 'formbuilder-migrations'])
        ->assertExitCode(0);

    $files = collect(File::files($directory))
        ->map(fn ($file) => $file->getFilename())
        ->sort()
        ->values();

    File::cleanDirectory($directory);

    expect($files)->toHaveCount(2)
        ->and($files[0])->toEndWith('_create_formbuilder_tables.php')
        ->and($files[1])->toEndWith('_add_is_required_to_form_items_table.php');
});

it('publishes the configuration', function () {
    $this->artisan('vendor:publish', ['--tag' => 'formbuilder-config'])
        ->assertExitCode(0);

    expect(File::exists(config_path('formbuilder.php')))->toBeTrue();

    File::delete(config_path('formbuilder.php'));
});
