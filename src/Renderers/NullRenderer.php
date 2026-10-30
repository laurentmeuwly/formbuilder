<?php

namespace LaurentMeuwly\FormBuilder\Renderers;

use LaurentMeuwly\FormBuilder\Contracts\RendersForm;
use LaurentMeuwly\FormBuilder\Models\Form;

class NullRenderer implements RendersForm
{
    /**
     * UI-agnostic representation: the form and its items as arrays.
     *
     * @param  array<string, mixed>  $context
     * @return array{form: array<string, mixed>, items: list<array<string, mixed>>}
     */
    public function render(Form $form, array $context = []): array
    {
        return [
            'form' => $form->toArray(),
            'items' => $form->items->map->toArray()->all(),
        ];
    }
}
