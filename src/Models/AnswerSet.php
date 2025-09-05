<?php

namespace LaurentMeuwly\FormBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnswerSet extends Model
{
    protected $table = 'fb_answer_sets';

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
        return $this->belongsTo(Form::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function answerable()
    {
        return $this->morphTo();
    }
}