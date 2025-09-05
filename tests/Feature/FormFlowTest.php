<?php

declare(strict_types=1);

namespace LaurentMeuwly\FormBuilder\Tests\Feature;

use Illuminate\Support\Facades\Schema;
use LaurentMeuwly\FormBuilder\Enums\FormFieldType;
use LaurentMeuwly\FormBuilder\Models\{Form, FormItem, BranchingRule, AnswerSet};
use LaurentMeuwly\FormBuilder\Renderers\NullRenderer;
use LaurentMeuwly\FormBuilder\Support\{BranchingEvaluator, ValidationRuleBuilder};
use LaurentMeuwly\FormBuilder\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use LaurentMeuwly\FormBuilder\Traits\HasDynamicForm;

uses(TestCase::class);

// Modèle métier factice pour le test
class TestParticipant extends Model
{
    use HasDynamicForm;

    protected $table = 'test_participants';
    protected $guarded = [];
}

it('runs full flow: migrations, CRUD, rendering, validation, answers', function () {
    // 1) Vérifie que les tables configurées existent
    expect(Schema::hasTable(config('formbuilder.table_names.forms')))->toBeTrue();
    expect(Schema::hasTable(config('formbuilder.table_names.form_items')))->toBeTrue();
    expect(Schema::hasTable(config('formbuilder.table_names.branching_rules')))->toBeTrue();
    expect(Schema::hasTable(config('formbuilder.table_names.answer_sets')))->toBeTrue();
    expect(Schema::hasTable(config('formbuilder.table_names.answers')))->toBeTrue();

    // 2) Crée un formulaire + champs + règle
    $form = Form::create([
        'key'       => 'technical_questionnaire_v1',
        'title'     => 'Questionnaire technique',        
        'is_active' => true,
    ]);

    $form->items()->createMany([
        [
            'key'       => 'sample_type',
            'label'     => "Type d'échantillon",
            'type'      => FormFieldType::SELECT,
            'position'  => 1,
            'options'   => [
                ['value' => 'solid',  'label' => 'Solide'],
                ['value' => 'liquid', 'label' => 'Liquide'],
            ],
            'validation'=> ['required' => true],
        ],
        [
            'key'       => 'granulometry',
            'label'     => 'Granulométrie (µm)',
            'type'      => FormFieldType::NUMBER,
            'position'  => 2,
            'validation'=> ['min' => 0],
        ],
    ]);

    $form->branchingRules()->create([
        'condition' => [
            'if'   => ['field' => 'sample_type', 'op' => '=', 'value' => 'solid'],
            'then' => ['show' => ['granulometry']],
        ],
    ]);

    // 3) Rendering via NullRenderer (agnostique UI)
    $schema = (new NullRenderer())->render($form);
    expect($schema)->toHaveKeys(['form', 'items']);
    expect($schema['items'])->toBeArray()->and($schema['items'])->toHaveCount(2);

    // 4) Règles de validation dynamiques
    $rules = ValidationRuleBuilder::for($form);
    expect($rules['sample_type'])->toContain('required');
    expect($rules['granulometry'])->toContain('nullable', 'min:0');

    // 5) Visibilité dynamique
    $visible = BranchingEvaluator::visibleKeys($form, ['sample_type' => 'solid']);
    expect($visible)->toContain('granulometry');

    // 6) Sauvegarde des réponses via un modèle métier + trait
    $participant = TestParticipant::create(['name' => 'Lab X']);
    /** @var AnswerSet $set */
    $set = $participant->startAnswerSet($form, ['context' => 'demo']);

    $set->answers()->createMany([
        ['field_key' => 'sample_type',  'value' => 'solid'],
        ['field_key' => 'granulometry', 'value' => 12],
    ]);

    expect($set->answers()->count())->toBe(2);
});
