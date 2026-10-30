<?php

namespace LaurentMeuwly\FormBuilder\Support;

use Illuminate\Validation\Rule;
use LaurentMeuwly\FormBuilder\Enums\FormFieldType;
use LaurentMeuwly\FormBuilder\Models\Form;

class ValidationRuleBuilder
{
    /**
     * Build Laravel validation rules for every item of the form, keyed by
     * item key.
     *
     * @return array<string, list<mixed>>
     */
    public static function for(Form $form): array
    {
        $rules = [];

        foreach ($form->items as $item) {
            $v = $item->validation ?? [];

            $r = [$item->isRequired() ? 'required' : 'nullable'];

            // Type rules give min/max their numeric/array meaning
            // (without them, Laravel compares string lengths).
            $typeRule = match ($item->type) {
                FormFieldType::NUMBER => 'numeric',
                FormFieldType::DATE => 'date',
                FormFieldType::CHECKBOX => 'array',
                default => null,
            };

            if ($typeRule !== null) {
                $r[] = $typeRule;
            }

            if (isset($v['min'])) {
                $r[] = 'min:'.$v['min'];
            }

            if (isset($v['max'])) {
                $r[] = 'max:'.$v['max'];
            }

            if (isset($v['regex'])) {
                $r[] = 'regex:'.$v['regex'];
            }

            if (isset($v['in']) && is_array($v['in'])) {
                $r[] = Rule::in($v['in']);
            }

            $rules[$item->key] = $r;
        }

        return $rules;
    }
}
