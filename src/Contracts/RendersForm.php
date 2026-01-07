<?php

namespace LaurentMeuwly\FormBuilder\Contracts;

use LaurentMeuwly\FormBuilder\Models\Form;

interface RendersForm
{
    /** Returns a representation that can be consumed by a UI (e.g., Filament schema) */
    public function render(Form $form, array $context = []): mixed;
}