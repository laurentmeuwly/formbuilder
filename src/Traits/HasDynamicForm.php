<?php

namespace LaurentMeuwly\FormBuilder\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use LaurentMeuwly\FormBuilder\Models\AnswerSet;
use LaurentMeuwly\FormBuilder\Models\Form;

trait HasDynamicForm
{
    public function formAnswerSets(): MorphMany
    {
        return $this->morphMany(AnswerSet::class, 'answerable');
    }

    public function startAnswerSet(Form $form, array $meta = []): AnswerSet
    {
        return $this->formAnswerSets()->create([
            'form_id' => $form->id,
            'meta' => $meta,
        ]);
    }
}