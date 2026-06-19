<?php

namespace LaurentMeuwly\FormBuilder\Facades;

use Illuminate\Support\Facades\Facade;
use LaurentMeuwly\FormBuilder\Contracts\RendersForm;

class FormBuilder extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return RendersForm::class;
    }
}
