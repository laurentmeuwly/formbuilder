<?php

namespace LaurentMeuwly\FormBuilder\Support;

use LaurentMeuwly\FormBuilder\Models\Form;

class BranchingEvaluator
{
    /** Retourne les clés visibles selon les réponses partielles */
    public static function visibleKeys(Form $form, array $answers): array
    {
        // Questions targeted by a "show" are hidden by default
        $conditionallyShown = collect($form->branchingRules)
            ->flatMap(function ($rule) {
                $then = $rule->condition['then'] ?? [];

                if (!empty($then['show'])) {
                    return $then['show'];
                }

                if (($then['action'] ?? null) === 'show' && !empty($then['targets'])) {
                    return $then['targets'];
                }

                return [];
            })
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
            $c = $rule->condition['if'] ?? null;
            $then = $rule->condition['then'] ?? [];

            if (! $c || ! isset($c['field'], $c['op'])) {
                continue;
            }

            $actual = $answers[$c['field']] ?? null;

            if (! self::match($actual, $c['op'], $c['value'] ?? null)) {
                continue;
            }

            foreach ($then['show'] ?? [] as $key) {
                $visible[$key] = true;
            }

            if (($then['action'] ?? null) === 'show') {
                foreach ($then['targets'] ?? [] as $key) {
                    $visible[$key] = true;
                }
            }

            foreach ($then['hide'] ?? [] as $key) {
                $visible[$key] = false;
            }
        }

        return array_keys(array_filter($visible));
    }

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
                is_array($actual)   => in_array($expected, $actual, true),   // checkbox
                is_string($actual) => str_contains($actual, (string) $expected),
                default             => false,
            },

            default => false,
        };
    }
}