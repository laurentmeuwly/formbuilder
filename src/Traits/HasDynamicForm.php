<?php

namespace LaurentMeuwly\FormBuilder\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use LaurentMeuwly\FormBuilder\Models\AnswerSet;
use LaurentMeuwly\FormBuilder\Models\Form;

/**
 * Add to any Eloquent model that answers forms (participant, order, ...).
 */
trait HasDynamicForm
{
    public function formAnswerSets(): MorphMany
    {
        return $this->morphMany(config('formbuilder.models.answer_set', AnswerSet::class), 'answerable');
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public function startAnswerSet(Form $form, array $meta = []): AnswerSet
    {
        /** @var AnswerSet $set */
        $set = $this->formAnswerSets()->create([
            'form_id' => $form->getKey(),
            'meta' => $meta,
        ]);

        return $set;
    }
}
