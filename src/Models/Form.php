<?php

namespace LaurentMeuwly\FormBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use LaurentMeuwly\FormBuilder\Traits\UsesConfiguredTable;

class Form extends Model
{
    use UsesConfiguredTable;

    public const TABLE_CONFIG_KEY = 'forms';

    protected $fillable = [
        'key',      // ex: technical_questionnaire
        'title',        
        'meta',     // json: description, help, etc.
        'is_active',
    ];

    protected $casts = [
        'meta' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Form $form) {
            if (empty($form->key)) {
                $form->key = Str::slug($form->title ?? 'form') ?: (string) Str::uuid();

                $suffix = 1;
                while (self::where('key', $form->key)->exists()) {
                    $form->key = $form->key.'_'.$suffix++;
                }
            }
        });
    }

    public function items(): HasMany
    {
        $itemClass = config('formbuilder.models.form_item');
        return $this->hasMany($itemClass, 'form_id')->orderBy('position');
    }

    public function branchingRules(): HasMany
    {
        $ruleClass = config('formbuilder.models.branching');
        return $this->hasMany($ruleClass, 'form_id');
    }

    public function answerSets(): HasMany
    {
        $setClass = config('formbuilder.models.answer_set');
        return $this->hasMany($setClass, 'form_id');
    }
}