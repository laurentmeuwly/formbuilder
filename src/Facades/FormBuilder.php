<?php

namespace LaurentMeuwly\FormBuilder\Facades;

use Illuminate\Support\Facades\Facade;

class FormBuilder extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \LaurentMeuwly\FormBuilder\Contracts\RendersForm::class;
    }
}