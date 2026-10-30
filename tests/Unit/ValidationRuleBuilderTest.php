<?php

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\In;
use LaurentMeuwly\FormBuilder\Enums\FormFieldType;
use LaurentMeuwly\FormBuilder\Support\ValidationRuleBuilder;

it('marks items as required or nullable', function () {
    $form = makeForm([
        ['key' => 'by_rule', 'validation' => ['required' => true]],
        ['key' => 'by_column', 'is_required' => true],
        ['key' => 'optional', 'validation' => ['required' => false]],
        ['key' => 'no_validation'],
    ]);

    $rules = ValidationRuleBuilder::for($form);

    expect($rules['by_rule'][0])->toBe('required')
        ->and($rules['by_column'][0])->toBe('required')
        ->and($rules['optional'][0])->toBe('nullable')
        ->and($rules['no_validation'])->toBe(['nullable']);
});

it('adds min, max, regex and in rules', function () {
    $form = makeForm([
        ['key' => 'name', 'validation' => ['min' => 3, 'max' => 50, 'regex' => '/^[a-z]+$/']],
        ['key' => 'level', 'validation' => ['in' => ['low', 'high']]],
    ]);

    $rules = ValidationRuleBuilder::for($form);

    expect($rules['name'])->toBe(['nullable', 'min:3', 'max:50', 'regex:/^[a-z]+$/'])
        ->and($rules['level'][1])->toBeInstanceOf(In::class);
});

it('adds a type rule for numbers, dates and checkboxes', function (FormFieldType $type, ?string $expected) {
    $form = makeForm([['key' => 'field', 'type' => $type]]);

    expect(ValidationRuleBuilder::for($form)['field'])
        ->toBe($expected === null ? ['nullable'] : ['nullable', $expected]);
})->with([
    [FormFieldType::NUMBER, 'numeric'],
    [FormFieldType::DATE, 'date'],
    [FormFieldType::CHECKBOX, 'array'],
    [FormFieldType::TEXT, null],
    [FormFieldType::SELECT, null],
]);

it('produces rules that Laravel validates as expected', function () {
    $form = makeForm([
        ['key' => 'sample_type', 'type' => FormFieldType::SELECT, 'is_required' => true, 'validation' => ['in' => ['solid', 'liquid']]],
        ['key' => 'mass', 'type' => FormFieldType::NUMBER, 'validation' => ['min' => 10]],
    ]);

    $rules = ValidationRuleBuilder::for($form);

    expect(Validator::make(['sample_type' => 'solid', 'mass' => '12'], $rules)->passes())->toBeTrue()
        // Compared as a number (5 < 10), not as a string length
        ->and(Validator::make(['sample_type' => 'solid', 'mass' => '5'], $rules)->fails())->toBeTrue()
        ->and(Validator::make(['sample_type' => 'gas'], $rules)->fails())->toBeTrue()
        ->and(Validator::make([], $rules)->errors()->keys())->toBe(['sample_type']);
});
