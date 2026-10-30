<?php

use Illuminate\Support\Facades\Schema;
use LaurentMeuwly\FormBuilder\Enums\FormFieldType;
use LaurentMeuwly\FormBuilder\Models\AnswerSet;
use LaurentMeuwly\FormBuilder\Models\Form;
use LaurentMeuwly\FormBuilder\Support\BranchingEvaluator;
use LaurentMeuwly\FormBuilder\Support\ValidationRuleBuilder;
use LaurentMeuwly\FormBuilder\Tests\Fixtures\Participant;

it('creates the configured tables', function () {
    foreach (config('formbuilder.table_names') as $table) {
        expect(Schema::hasTable($table))->toBeTrue();
    }

    expect(Schema::hasColumn(config('formbuilder.table_names.form_items'), 'is_required'))->toBeTrue();
});

it('runs a complete flow: definition, branching, validation and answers', function () {
    $form = Form::create(['key' => 'technical_questionnaire_v1', 'title' => 'Questionnaire technique']);

    $form->items()->createMany([
        [
            'key' => 'sample_type',
            'label' => "Type d'échantillon",
            'type' => FormFieldType::SELECT,
            'position' => 1,
            'is_required' => true,
            'options' => [
                ['value' => 'solid', 'label' => 'Solide'],
                ['value' => 'liquid', 'label' => 'Liquide'],
            ],
        ],
        [
            'key' => 'granulometry',
            'label' => 'Granulométrie (µm)',
            'type' => FormFieldType::NUMBER,
            'position' => 2,
            'validation' => ['min' => 0],
        ],
    ]);

    $form->branchingRules()->create([
        'condition' => [
            'if' => ['field' => 'sample_type', 'op' => '=', 'value' => 'solid'],
            'then' => ['show' => ['granulometry']],
        ],
    ]);

    $form = $form->fresh(['items', 'branchingRules']);

    expect(ValidationRuleBuilder::for($form))->toMatchArray([
        'sample_type' => ['required'],
        'granulometry' => ['nullable', 'numeric', 'min:0'],
    ])
        ->and(BranchingEvaluator::visibleKeys($form, ['sample_type' => 'solid']))->toBe(['sample_type', 'granulometry'])
        ->and(BranchingEvaluator::visibleKeys($form, ['sample_type' => 'liquid']))->toBe(['sample_type']);

    $participant = Participant::create(['name' => 'Lab X']);
    $set = $participant->startAnswerSet($form, ['context' => 'demo']);

    $set->answers()->createMany([
        ['field_key' => 'sample_type', 'value' => 'solid'],
        ['field_key' => 'granulometry', 'value' => 12],
    ]);

    $set = AnswerSet::with(['answers', 'answerable', 'form'])->findOrFail($set->id);

    expect($set->meta)->toBe(['context' => 'demo'])
        ->and($set->form->is($form))->toBeTrue()
        ->and($set->answerable->is($participant))->toBeTrue()
        // Row order is not guaranteed without ORDER BY: compare sorted by key
        ->and($set->answers->pluck('value', 'field_key')->sortKeys()->all())->toBe([
            'granulometry' => 12,
            'sample_type' => 'solid',
        ])
        ->and($participant->formAnswerSets()->count())->toBe(1)
        ->and($form->answerSets()->count())->toBe(1);
});
