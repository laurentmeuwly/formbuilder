<?php

namespace LaurentMeuwly\FormBuilder\Renderers;

use LaurentMeuwly\FormBuilder\Contracts\RendersForm;
use LaurentMeuwly\FormBuilder\Models\Form;

class NullRenderer implements RendersForm
{
    public function render(Form $form, array $context = []): array
    {
        return [
            'form' => $form->toArray(),
            'items' => $form->items->map->toArray()->all(),
        ];
    }
}