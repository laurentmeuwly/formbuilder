<?php

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use LaurentMeuwly\FormBuilder\Enums\FormFieldType;
use LaurentMeuwly\FormBuilder\Renderers\FilamentFormRenderer;

beforeEach(function () {
    if (! class_exists(TextInput::class)) {
        $this->markTestSkipped('filament/forms is not installed.');
    }
});

it('maps every field type to a Filament component', function () {
    $options = [['value' => 'a', 'label' => 'A'], ['value' => 'b', 'label' => 'B']];

    $form = makeForm([
        ['key' => 'text', 'label' => 'Text', 'type' => FormFieldType::TEXT],
        ['key' => 'textarea', 'label' => 'Textarea', 'type' => FormFieldType::TEXTAREA],
        ['key' => 'number', 'label' => 'Number', 'type' => FormFieldType::NUMBER],
        ['key' => 'select', 'label' => 'Select', 'type' => FormFieldType::SELECT, 'options' => $options],
        ['key' => 'radio', 'label' => 'Radio', 'type' => FormFieldType::RADIO, 'options' => $options],
        ['key' => 'checkbox', 'label' => 'Checkbox', 'type' => FormFieldType::CHECKBOX, 'options' => $options],
        ['key' => 'date', 'label' => 'Date', 'type' => FormFieldType::DATE],
        ['key' => 'file', 'label' => 'File', 'type' => FormFieldType::FILE],
    ]);

    $components = (new FilamentFormRenderer)->render($form);

    expect(array_map(fn ($component) => $component::class, $components))->toBe([
        TextInput::class,
        Textarea::class,
        TextInput::class,
        Select::class,
        Radio::class,
        CheckboxList::class,
        DatePicker::class,
        FileUpload::class,
    ])
        ->and($components[0]->getName())->toBe('text')
        ->and($components[0]->getLabel())->toBe('Text');
});

it('marks required fields from either source', function () {
    $form = makeForm([
        ['key' => 'by_column', 'type' => FormFieldType::TEXTAREA, 'is_required' => true],
        ['key' => 'by_rule', 'type' => FormFieldType::NUMBER, 'validation' => ['required' => true]],
        ['key' => 'optional', 'type' => FormFieldType::TEXT],
    ]);

    $components = (new FilamentFormRenderer)->render($form);

    expect($components[0]->isRequired())->toBeTrue()
        ->and($components[1]->isRequired())->toBeTrue()
        ->and($components[2]->isRequired())->toBeFalse();
});

it('skips items without a type', function () {
    $form = makeForm([
        ['key' => 'typed', 'type' => FormFieldType::TEXT],
        ['key' => 'untyped'],
    ]);

    expect((new FilamentFormRenderer)->render($form))->toHaveCount(1);
});
