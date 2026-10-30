<?php

namespace LaurentMeuwly\FormBuilder\Contracts;

use LaurentMeuwly\FormBuilder\Models\Form;

interface RendersForm
{
    /**
     * Return a representation that a UI can consume (e.g. a Filament schema).
     *
     * @param  array<string, mixed>  $context
     */
    public function render(Form $form, array $context = []): mixed;
}
