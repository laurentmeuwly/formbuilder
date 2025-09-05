<?php

namespace LaurentMeuwly\FormBuilder\Models;

use LaurentMeuwly\FormBuilder\Traits\UsesConfiguredTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        return $this->hasMany(FormItem::class)->orderBy('position');
    }

    public function branchingRules(): HasMany
    {
        return $this->hasMany(BranchingRule::class);
    }
}