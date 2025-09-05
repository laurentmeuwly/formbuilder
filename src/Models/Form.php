<?php

namespace LaurentMeuwly\FormBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LaurentMeuwly\FormBuilder\Traits\UsesConfiguredTable;

class Form extends Model
{
    use UsesConfiguredTable;

    public const TABLE_CONFIG_KEY = 'forms';

    protected $fillable = [
        'key',      // ex: technical_questionnaire
        'title',
        'locale',   // fr, en, etc.
        'meta',     // json: description, help, etc.
        'is_active',
    ];

    protected $casts = [
        'meta' => 'array',
        'is_active' => 'boolean',
    ];

    public function items(): HasMany
    {
        $itemClass = config('formbuilder.models.form_item');
        return $this->hasMany($itemClass)->orderBy('position');
    }

    public function branchingRules(): HasMany
    {
        $ruleClass = config('formbuilder.models.branching');
        return $this->hasMany($ruleClass);
    }
}