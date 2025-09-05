<?php

declare(strict_types=1);

use LaurentMeuwly\FormBuilder\Models\Form;
use LaurentMeuwly\FormBuilder\Models\FormItem;
use LaurentMeuwly\FormBuilder\Support\ValidationRuleBuilder;

it('builds rules correctly', function () {
    $form = new Form(['id' => 1]);
    $form->setRelation('items', collect([
        new FormItem(['key' => 'name', 'validation' => ['required' => true, 'min' => 3]]),
        new FormItem(['key' => 'age',  'validation' => ['required' => false, 'min' => 0]]),
    ]));

    $rules = ValidationRuleBuilder::for($form);

    expect($rules['name'])->toContain('required', 'min:3');
    expect($rules['age'])->toContain('nullable', 'min:0');
});
