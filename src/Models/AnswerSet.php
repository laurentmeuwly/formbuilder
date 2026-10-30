<?php

namespace LaurentMeuwly\FormBuilder\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use LaurentMeuwly\FormBuilder\Traits\UsesConfiguredTable;

/**
 * @property int $id
 * @property int $form_id
 * @property string $answerable_type
 * @property int|string $answerable_id
 * @property Carbon|null $submitted_at
 * @property array<string, mixed>|null $meta
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Form $form
 * @property-read Collection<int, Answer> $answers
 * @property-read Model $answerable
 */
class AnswerSet extends Model
{
    use UsesConfiguredTable;

    public const TABLE_CONFIG_KEY = 'answer_sets';

    protected $fillable = [
        'form_id',
        'answerable_type',
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
        return $this->belongsTo(config('formbuilder.models.form'), 'form_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(config('formbuilder.models.answer'), 'answer_set_id');
    }

    public function answerable(): MorphTo
    {
        return $this->morphTo();
    }
}
