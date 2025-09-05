<?php

namespace LaurentMeuwly\FormBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LaurentMeuwly\FormBuilder\Traits\UsesConfiguredTable;

class AnswerSet extends Model
{
    use UsesConfiguredTable;

    public const TABLE_CONFIG_KEY = 'answer_sets';    

    protected $fillable = [
        'form_id',
        'answerable_type', // morph: votre entité métier (Participation, etc.)
        'answerable_id',
        'submitted_at',
        'meta',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'meta' => 'array',
    ];

    public function form(): BelongsTo
    {
        $formClass = config('formbuilder.models.form');
        return $this->belongsTo($formClass);
    }

    public function answers(): HasMany
    {
        $answerClass = config('formbuilder.models.answer');
        return $this->hasMany($answerClass);
    }

    public function answerable()
    {
        return $this->morphTo();
    }
}