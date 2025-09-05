<?php

namespace LaurentMeuwly\FormBuilder\Support;

use Illuminate\Validation\Rule;
use LaurentMeuwly\FormBuilder\Models\Form;

class ValidationRuleBuilder
{
    public static function for(Form $form): array
    {
        $rules = [];
        foreach ($form->items as $item) {
            $v = $item->validation ?? [];
            $r = [];
            if (($v['required'] ?? false) === true) {
                $r[] = 'required';
            } else {
                $r[] = 'nullable';
            }
            if (isset($v['min'])) $r[] = 'min:'.$v['min'];
            if (isset($v['max'])) $r[] = 'max:'.$v['max'];
            if (isset($v['regex'])) $r[] = 'regex:'.$v['regex'];
            if (isset($v['in']) && is_array($v['in'])) $r[] = Rule::in($v['in']);

            $rules[$item->key] = $r;
        }
        return $rules;
    }
}