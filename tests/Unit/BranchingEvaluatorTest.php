<?php

use LaurentMeuwly\FormBuilder\Support\BranchingEvaluator;

it('shows every item when there is no rule', function () {
    $form = makeForm(['q1', 'q2']);

    expect(BranchingEvaluator::visibleKeys($form, []))->toBe(['q1', 'q2']);
});

it('hides items targeted by a show rule by default', function () {
    $form = makeForm(['qa', 'q7'], [
        ['if' => ['field' => 'qa', 'op' => '=', 'value' => 'other'], 'then' => ['show' => ['q7']]],
    ]);

    expect(BranchingEvaluator::visibleKeys($form, []))->toBe(['qa']);
});

it('shows a targeted item when the condition matches', function () {
    $form = makeForm(['type', 'granulo'], [
        ['if' => ['field' => 'type', 'op' => '=', 'value' => 'solid'], 'then' => ['show' => ['granulo']]],
    ]);

    expect(BranchingEvaluator::visibleKeys($form, ['type' => 'solid']))->toBe(['type', 'granulo'])
        ->and(BranchingEvaluator::visibleKeys($form, ['type' => 'liquid']))->toBe(['type']);
});

it('hides an item when a hide rule matches', function () {
    $form = makeForm(['type', 'ph'], [
        ['if' => ['field' => 'type', 'op' => '=', 'value' => 'solid'], 'then' => ['hide' => ['ph']]],
    ]);

    expect(BranchingEvaluator::visibleKeys($form, ['type' => 'solid']))->toBe(['type'])
        ->and(BranchingEvaluator::visibleKeys($form, ['type' => 'liquid']))->toBe(['type', 'ph']);
});

it('gives hide precedence over show regardless of rule order', function (array $rules) {
    $form = makeForm(['a', 'b', 'target'], $rules);

    expect(BranchingEvaluator::visibleKeys($form, ['a' => 'yes', 'b' => 'yes']))
        ->not->toContain('target');
})->with([
    'show then hide' => [[
        ['if' => ['field' => 'a', 'op' => '=', 'value' => 'yes'], 'then' => ['show' => ['target']]],
        ['if' => ['field' => 'b', 'op' => '=', 'value' => 'yes'], 'then' => ['hide' => ['target']]],
    ]],
    'hide then show' => [[
        ['if' => ['field' => 'b', 'op' => '=', 'value' => 'yes'], 'then' => ['hide' => ['target']]],
        ['if' => ['field' => 'a', 'op' => '=', 'value' => 'yes'], 'then' => ['show' => ['target']]],
    ]],
]);

it('ignores malformed conditions', function (mixed $condition) {
    $form = makeForm(['q1', 'q2'], [
        ['if' => $condition, 'then' => ['hide' => ['q2']]],
    ]);

    expect(BranchingEvaluator::visibleKeys($form, ['q1' => 'x']))->toBe(['q1', 'q2']);
})->with([
    'missing if' => [null],
    'not an array' => ['q1 = x'],
    'missing op' => [['field' => 'q1', 'value' => 'x']],
    'missing field' => [['op' => '=', 'value' => 'x']],
]);

it('evaluates comparison operators', function (string $op, mixed $actual, mixed $expected, bool $matches) {
    $form = makeForm(['q1', 'q2'], [
        ['if' => ['field' => 'q1', 'op' => $op, 'value' => $expected], 'then' => ['show' => ['q2']]],
    ]);

    $visible = BranchingEvaluator::visibleKeys($form, ['q1' => $actual]);

    expect(in_array('q2', $visible, true))->toBe($matches);
})->with([
    '= equal' => ['=', 'solid', 'solid', true],
    '= different' => ['=', 'solid', 'liquid', false],
    '== loose' => ['==', '5', 5, true],
    '!= different' => ['!=', 'a', 'b', true],
    '<> equal' => ['<>', 'a', 'a', false],
    '> true' => ['>', 10, 5, true],
    '> false' => ['>', 5, 10, false],
    '> numeric string' => ['>', '10', '5', true],
    '> not numeric' => ['>', 'abc', 5, false],
    '< true' => ['<', 1, 2, true],
    '>= equal' => ['>=', 5, 5, true],
    '<= greater' => ['<=', 6, 5, false],
    'in checkbox contains' => ['in', ['a', 'other'], 'other', true],
    'in checkbox missing' => ['in', ['a', 'b'], 'other', false],
    'in list of values' => ['in', 'b', ['a', 'b'], true],
    'in list missing' => ['in', 'c', ['a', 'b'], false],
    'in substring' => ['in', 'some other text', 'other', true],
    'in null answer' => ['in', null, 'other', false],
    'unknown operator' => ['~', 'a', 'a', false],
]);

it('treats a missing answer as null', function () {
    $form = makeForm(['q1', 'q2'], [
        ['if' => ['field' => 'q1', 'op' => '=', 'value' => null], 'then' => ['hide' => ['q2']]],
    ]);

    expect(BranchingEvaluator::visibleKeys($form, []))->toBe(['q1']);
});
