<?php

namespace LaurentMeuwly\FormBuilder\Support;

use LaurentMeuwly\FormBuilder\Models\Form;

class BranchingEvaluator
{
    /** Retourne les clés visibles selon les réponses partielles */
    public static function visibleKeys(Form $form, array $answers): array
    {
        $visible = collect($form->items)->pluck('key')->values()->all();
        foreach ($form->branchingRules as $rule) {
            $c = $rule->condition['if'] ?? null;
            $then = $rule->condition['then'] ?? [];
            if (!$c) continue;
            $ok = self::match($answers[$c['field']] ?? null, $c['op'] ?? '=', $c['value'] ?? null);
            if ($ok) {
                if (!empty($then['show'])) $visible = array_values(array_unique(array_merge($visible, $then['show'])));
                if (!empty($then['hide'])) $visible = array_values(array_diff($visible, $then['hide']));
            }
        }
        return $visible;
    }

    protected static function match($actual, string $op, $expected): bool
    {
        return match ($op) {
            '=', '==' => $actual == $expected,
            '!=', '<>' => $actual != $expected,
            '>' => $actual > $expected,
            '<' => $actual < $expected,
            '>=' => $actual >= $expected,
            '<=' => $actual <= $expected,
            'in' => is_array($expected) ? in_array($actual, $expected, true) : false,
            default => false,
        };
    }
}