<?php

namespace LaurentMeuwly\FormBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use LaurentMeuwly\FormBuilder\Traits\UsesConfiguredTable;

/**
 * @property int $id
 * @property int $answer_set_id
 * @property string $field_key
 * @property mixed $value
 * @property array<string, mixed>|null $meta
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read AnswerSet $answerSet
 */
class Answer extends Model
{
    use UsesConfiguredTable;

    public const TABLE_CONFIG_KEY = 'answers';

    protected $fillable = [
        'answer_set_id',
        'field_key',    // stable reference to FormItem::key
        'value',        // any JSON value
        'meta',
    ];

    protected $casts = [
        'value' => 'array',
        'meta' => 'array',
    ];

    public function answerSet(): BelongsTo
    {
        return $this->belongsTo(config('formbuilder.models.answer_set'), 'answer_set_id');
    }
}
