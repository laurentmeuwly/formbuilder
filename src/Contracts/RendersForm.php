<?php

namespace LaurentMeuwly\FormBuilder\Contracts;

use LaurentMeuwly\FormBuilder\Models\Form;

interface RendersForm
{
    /** Retourne une représentation consommable par une UI (ex: schéma Filament) */
    public function render(Form $form, array $context = []): mixed;
}