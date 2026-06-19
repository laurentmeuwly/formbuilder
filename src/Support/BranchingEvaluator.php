<?php

namespace LaurentMeuwly\FormBuilder\Support;

use LaurentMeuwly\FormBuilder\Models\Form;

class BranchingEvaluator
{
    /**
     * Returns the list of visible keys based on the responses provided.
     */
    public static function visibleKeys(Form $form, array $answers): array
    {
        // Questions targeted by a "show" are hidden by default
        $conditionallyShown = collect($form->branchingRules)
            ->flatMap(fn ($rule) => $rule->condition['then']['show'] ?? [])
            ->unique()
            ->values()
            ->all();

        // Initializing visibility
        $visible = [];

        foreach ($form->items as $item) {
            $visible[$item->key] = ! in_array($item->key, $conditionallyShown, true);
        }

        // Enforcement of rules
        foreach ($form->branchingRules as $rule) {
            $condition = $rule->condition['if'] ?? null;
            $effects = $rule->condition['then'] ?? [];

            if (! self::isValidCondition($condition)) {
                continue;
            }

            $actual = $answers[$condition['field']] ?? null;

            if (! self::match($actual, $condition['op'], $condition['value'] ?? null)) {
                continue;
            }

            // SHOW
            foreach ($effects['show'] ?? [] as $key) {
                $visible[$key] = true;
            }

            // HIDE
            foreach ($effects['hide'] ?? [] as $key) {
                $visible[$key] = false;
            }
        }

        return array_keys(array_filter($visible));
    }

    /**
     * Verifies that a condition is usable.
     */
    protected static function isValidCondition(?array $condition): bool
    {
        return is_array($condition)
            && isset($condition['field'], $condition['op']);
    }

    /**
     * Evaluates a simple condition.
     */
    protected static function match($actual, string $op, $expected): bool
    {
        return match ($op) {
            '=', '==' => $actual == $expected,
            '!=', '<>' => $actual != $expected,
            '>' => is_numeric($actual) && is_numeric($expected) && $actual > $expected,
            '<' => is_numeric($actual) && is_numeric($expected) && $actual < $expected,
            '>=' => is_numeric($actual) && is_numeric($expected) && $actual >= $expected,
            '<=' => is_numeric($actual) && is_numeric($expected) && $actual <= $expected,

            'in' => match (true) {
                is_array($actual) => in_array($expected, $actual, true),   // checkbox
                is_string($actual) => str_contains($actual, (string) $expected),
                default => false,
            },

            default => false,
        };
    }
}
