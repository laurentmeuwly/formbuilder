<?php

namespace LaurentMeuwly\FormBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use LaurentMeuwly\FormBuilder\Traits\UsesConfiguredTable;

/**
 * Canonical condition format:
 * {"if": {"field": "sample_type", "op": "=", "value": "solid"},
 *  "then": {"show": ["granulometry"], "hide": ["ph"]}}
 *
 * @property int $id
 * @property int $form_id
 * @property array{if?: array{field?: string, op?: string, value?: mixed}, then?: array{show?: list<string>, hide?: list<string>}} $condition
 * @property array<string, mixed>|null $meta
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Form $form
 */
class BranchingRule extends Model
{
    use UsesConfiguredTable;

    public const TABLE_CONFIG_KEY = 'branching_rules';

    protected $fillable = [
        'form_id',
        'condition',
        'meta',
    ];

    protected $casts = [
        'condition' => 'array',
        'meta' => 'array',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(config('formbuilder.models.form'), 'form_id');
    }
}
