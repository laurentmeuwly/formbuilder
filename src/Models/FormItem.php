<?php

namespace LaurentMeuwly\FormBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use LaurentMeuwly\FormBuilder\Enums\FormFieldType;
use LaurentMeuwly\FormBuilder\Traits\UsesConfiguredTable;

class FormItem extends Model
{
    use UsesConfiguredTable;

    public const TABLE_CONFIG_KEY = 'form_items';

    protected $fillable = [
        'form_id',
        'key',               // ex: sample_mass or UUID
        'label',
        'type',              // enum ItemType
        'position',
        'is_required',
        'options',           // json (for select/radio/checkbox)
        'validation',        // json (min, max, pattern, ...)
        'meta',              // json (help, placeholder, unit, ...)
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
        static::creating(function (self $item) {
            if (empty($item->key)) {
                $item->key = (string) Str::uuid();                
            }

            if (empty($item->label)) {
                $item->label = 'item';
            }
        });
    }
    
    public function form(): BelongsTo
    {
        $formClass = config('formbuilder.models.form');
        return $this->belongsTo($formClass, 'form_id');
    }
}