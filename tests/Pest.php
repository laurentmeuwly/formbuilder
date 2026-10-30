<?php

use LaurentMeuwly\FormBuilder\Models\BranchingRule;
use LaurentMeuwly\FormBuilder\Models\Form;
use LaurentMeuwly\FormBuilder\Models\FormItem;
use LaurentMeuwly\FormBuilder\Tests\TestCase;

uses(TestCase::class)->in('Feature', 'Unit');

/**
 * Build an unsaved form with its relations set in memory.
 *
 * @param  array<int, string|array<string, mixed>>  $items  item keys, or item attributes
 * @param  array<int, array<string, mixed>>  $rules  canonical rule conditions
 */
function makeForm(array $items, array $rules = []): Form
{
    $form = new Form(['key' => 'test', 'title' => 'Test']);

    $form->setRelation('items', collect($items)->map(
        fn ($item) => new FormItem(is_string($item) ? ['key' => $item] : $item)
    ));

    $form->setRelation('branchingRules', collect($rules)->map(
        fn (array $condition) => new BranchingRule(['condition' => $condition])
    ));

    return $form;
}
