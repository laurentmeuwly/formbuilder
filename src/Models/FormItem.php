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

    protected static function booted(): void
    {
        static::creating(function (FormItem $item) {
            if (empty($item->key)) {
                
                $base = Str::slug($item->label ?? 'item');
                $item->key = $base ?: (string) Str::uuid();

                // If necessary, check uniqueness in the parent form.
                $suffix = 1;
                while (self::where('form_id', $item->form_id)
                        ->where('key', $item->key)
                        ->exists()) {
                    $item->key = $base.'_'.$suffix++;
                }
            }
        });
    }
    
    public function form(): BelongsTo
    {
        $formClass = config('formbuilder.models.form');
        return $this->belongsTo($formClass, 'form_id');
    }
}