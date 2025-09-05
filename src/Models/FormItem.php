<?php

namespace LaurentMeuwly\FormBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LaurentMeuwly\FormBuilder\Enums\FormFieldType;

class FormItem extends Model
{
    protected $table = 'fb_form_items';

    protected $fillable = [
        'form_id',
        'key',               // ex: sample_mass
        'label',
        'type',              // enum ItemType
        'position',
        'options',           // json (pour select/radio/checkbox)
        'validation',        // json (required, min, max, pattern, etc.)
        'meta',              // json (help, placeholder, unit, etc.)
    ];

    protected $casts = [
        'type' => FormFieldType::class,
        'options' => 'array',
        'validation' => 'array',
        'meta' => 'array',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}