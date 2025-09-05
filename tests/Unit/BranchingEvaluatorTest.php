<?php

declare(strict_types=1);

use LaurentMeuwly\FormBuilder\Models\Form;
use LaurentMeuwly\FormBuilder\Models\FormItem;
use LaurentMeuwly\FormBuilder\Models\BranchingRule;
use LaurentMeuwly\FormBuilder\Support\BranchingEvaluator;

it('computes visible keys based on simple condition', function () {
    $form = new Form();

    $form->setRelation('items', collect([
        new FormItem(['key' => 'type']),
        new FormItem(['key' => 'granulo']),
    ]));

    $form->setRelation('branchingRules', collect([
        new BranchingRule([
            'condition' => [
                'if'   => ['field' => 'type', 'op' => '=', 'value' => 'solid'],
                'then' => ['show' => ['granulo']],
            ],
        ]),
    ]));

    $visible = BranchingEvaluator::visibleKeys($form, ['type' => 'solid']);

    expect($visible)->toContain('granulo');
});
