<?php

use LaurentMeuwly\FormBuilder\Enums\FormFieldType;
use LaurentMeuwly\FormBuilder\Models\Answer;
use LaurentMeuwly\FormBuilder\Models\AnswerSet;
use LaurentMeuwly\FormBuilder\Models\BranchingRule;
use LaurentMeuwly\FormBuilder\Models\Form;
use LaurentMeuwly\FormBuilder\Models\FormItem;
use LaurentMeuwly\FormBuilder\Tests\Fixtures\Participant;

it('generates a unique key from the title', function () {
    $first = Form::create(['title' => 'Questionnaire technique']);
    $second = Form::create(['title' => 'Questionnaire technique']);
    $third = Form::create(['title' => 'Questionnaire technique']);

    expect($first->key)->toBe('questionnaire-technique')
        ->and($second->key)->toBe('questionnaire-technique_1')
        ->and($third->key)->toBe('questionnaire-technique_2');
});

it('keeps an explicit form key', function () {
    expect(Form::create(['key' => 'custom', 'title' => 'Anything'])->key)->toBe('custom');
});

it('fills a missing item key and label', function () {
    $form = Form::create(['title' => 'Form']);

    $item = $form->items()->create(['type' => FormFieldType::TEXT]);

    expect($item->key)->toMatch('/^[0-9a-f-]{36}$/')
        ->and($item->label)->toBe('item');
});

it('orders items by position', function () {
    $form = Form::create(['title' => 'Form']);

    $form->items()->createMany([
        ['key' => 'third', 'type' => FormFieldType::TEXT, 'position' => 3],
        ['key' => 'first', 'type' => FormFieldType::TEXT, 'position' => 1],
        ['key' => 'second', 'type' => FormFieldType::TEXT, 'position' => 2],
    ]);

    expect($form->items->pluck('key')->all())->toBe(['first', 'second', 'third']);
});

it('reports whether an item is required', function (array $attributes, bool $required) {
    expect((new FormItem($attributes))->isRequired())->toBe($required);
})->with([
    'nothing' => [[], false],
    'is_required column' => [['is_required' => true], true],
    'validation rule' => [['validation' => ['required' => true]], true],
    'validation false' => [['validation' => ['required' => false]], false],
]);

it('stores any JSON value as an answer', function (mixed $value) {
    $form = Form::create(['title' => 'Form']);
    $set = Participant::create(['name' => 'P'])->startAnswerSet($form);

    $answer = $set->answers()->create(['field_key' => 'q', 'value' => $value]);

    expect(Answer::findOrFail($answer->id)->value)->toBe($value);
})->with([
    'string' => ['solid'],
    'integer' => [12],
    'list' => [['a', 'b']],
    'null' => [null],
]);

it('uses the table names from the configuration', function () {
    expect((new Form)->getTable())->toBe('fb_forms')
        ->and((new FormItem)->getTable())->toBe('fb_form_items')
        ->and((new BranchingRule)->getTable())->toBe('fb_branching_rules')
        ->and((new AnswerSet)->getTable())->toBe('fb_answer_sets')
        ->and((new Answer)->getTable())->toBe('fb_answers');

    config()->set('formbuilder.table_names.forms', 'app_forms');

    expect((new Form)->getTable())->toBe('app_forms');
});

it('deletes items and rules with their form', function () {
    $form = Form::create(['title' => 'Form']);
    $form->items()->create(['key' => 'q1', 'type' => FormFieldType::TEXT]);
    $form->branchingRules()->create(['condition' => ['if' => ['field' => 'q1', 'op' => '=', 'value' => 'x']]]);

    $form->delete();

    expect(FormItem::count())->toBe(0)
        ->and(BranchingRule::count())->toBe(0);
});
