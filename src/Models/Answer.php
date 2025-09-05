<?php

namespace LaurentMeuwly\FormBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LaurentMeuwly\FormBuilder\Traits\UsesConfiguredTable;

class Answer extends Model
{
    use UsesConfiguredTable;

    public const TABLE_CONFIG_KEY = 'answers';    

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
        $answerSetClass = config('formbuilder.models.answer_set');
        return $this->belongsTo($answerSetClass, 'answer_set_id');
    }
}