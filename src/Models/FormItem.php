<?php

namespace LaurentMeuwly\FormBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use LaurentMeuwly\FormBuilder\Enums\FormFieldType;
use LaurentMeuwly\FormBuilder\Traits\UsesConfiguredTable;

/**
 * @property int $id
 * @property int $form_id
 * @property string $key
 * @property string $label
 * @property FormFieldType|null $type
 * @property int $position
 * @property bool $is_required
 * @property list<array{value: mixed, label: string}>|null $options
 * @property array<string, mixed>|null $validation
 * @property array<string, mixed>|null $meta
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Form $form
 */
class FormItem extends Model
{
    use UsesConfiguredTable;

    public const TABLE_CONFIG_KEY = 'form_items';

    protected $fillable = [
        'form_id',
        'key',          // stable identifier, e.g. "sample_mass" (UUID when empty)
        'label',
        'type',         // FormFieldType
        'position',
        'is_required',
        'options',      // json, for select / radio / checkbox
        'validation',   // json: required, min, max, regex, in
        'meta',         // json: help, placeholder, unit, ...
    ];

    protected $casts = [
        'type' => FormFieldType::class,
        'is_required' => 'boolean',
        'options' => 'array',
        'validation' => 'array',
        'meta' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (FormItem $item) {
            if (empty($item->key)) {
                $item->key = (string) Str::uuid();
            }

            if (empty($item->label)) {
                $item->label = 'item';
            }
        });
    }

    /**
     * Whether the item is mandatory, either through the `is_required`
     * column or the `required` key of its validation rules.
     */
    public function isRequired(): bool
    {
        return (bool) $this->is_required
            || (($this->validation['required'] ?? false) === true);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(config('formbuilder.models.form'), 'form_id');
    }
}
