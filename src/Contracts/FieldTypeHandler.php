<?php

namespace LaurentMeuwly\FormBuilder\Contracts;

use LaurentMeuwly\FormBuilder\Models\FormItem;

interface FieldTypeHandler
{
    public function toRenderable(FormItem $item): mixed;

    /**
     * @return list<mixed>
     */
    public function rules(FormItem $item): array;
}
