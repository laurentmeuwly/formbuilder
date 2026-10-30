<?php

namespace LaurentMeuwly\FormBuilder\Support;

use LaurentMeuwly\FormBuilder\Models\Form;

class BranchingEvaluator
{
    /**
     * Return the keys of the items visible for the given answers.
     *
     * - Items targeted by at least one `show` are hidden by default.
     * - An item is hidden as soon as one matching rule hides it: `hide`
     *   always takes precedence over `show`, whatever the rule order.
     *
     * @param  array<string, mixed>  $answers
     * @return list<string>
     */
    public static function visibleKeys(Form $form, array $answers): array
    {
        $conditionallyShown = collect($form->branchingRules)
            ->flatMap(fn ($rule) => $rule->condition['then']['show'] ?? [])
            ->unique()
            ->values()
            ->all();

        $visible = [];

        foreach ($form->items as $item) {
            $visible[$item->key] = ! in_array($item->key, $conditionallyShown, true);
        }

        $shown = [];
        $hidden = [];

        foreach ($form->branchingRules as $rule) {
            $condition = $rule->condition['if'] ?? null;
            $effects = $rule->condition['then'] ?? [];

            if (! is_array($condition) || ! isset($condition['field'], $condition['op'])) {
                continue;
            }

            $actual = $answers[$condition['field']] ?? null;

            if (! self::match($actual, (string) $condition['op'], $condition['value'] ?? null)) {
                continue;
            }

            foreach ((array) ($effects['show'] ?? []) as $key) {
                $shown[] = $key;
            }

            foreach ((array) ($effects['hide'] ?? []) as $key) {
                $hidden[] = $key;
            }
        }

        foreach ($shown as $key) {
            $visible[$key] = true;
        }

        foreach ($hidden as $key) {
            $visible[$key] = false;
        }

        return array_keys(array_filter($visible));
    }

    /**
     * Evaluate a single comparison.
     */
    protected static function match(mixed $actual, string $op, mixed $expected): bool
    {
        return match ($op) {
            '=', '==' => $actual == $expected,
            '!=', '<>' => $actual != $expected,
            '>' => is_numeric($actual) && is_numeric($expected) && $actual > $expected,
            '<' => is_numeric($actual) && is_numeric($expected) && $actual < $expected,
            '>=' => is_numeric($actual) && is_numeric($expected) && $actual >= $expected,
            '<=' => is_numeric($actual) && is_numeric($expected) && $actual <= $expected,

            'in' => match (true) {
                // Checkbox answer contains the expected value
                is_array($actual) => in_array($expected, $actual, true),
                // Scalar answer is one of the expected values
                is_array($expected) => in_array($actual, $expected),
                // Text answer contains the expected substring
                is_string($actual) && is_scalar($expected) => str_contains($actual, (string) $expected),
                default => false,
            },

            default => false,
        };
    }
}
