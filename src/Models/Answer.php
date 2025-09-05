<?php

namespace LaurentMeuwly\FormBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    protected $table = 'fb_answers';

    protected $fillable = [
        'answer_set_id',
        'field_key',     // référence stable vers FormItem::key
        'value',         // mixte (json)
        'meta',
    ];

    protected $casts = [
        'value' => 'array',
        'meta' => 'array',
    ];

    public function answerSet(): BelongsTo
    {
        return $this->belongsTo(AnswerSet::class, 'answer_set_id');
    }
}