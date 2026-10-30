<?php

namespace LaurentMeuwly\FormBuilder\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use LaurentMeuwly\FormBuilder\Traits\UsesConfiguredTable;

/**
 * @property int $id
 * @property string $key
 * @property string $title
 * @property array<string, mixed>|null $meta
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, FormItem> $items
 * @property-read Collection<int, BranchingRule> $branchingRules
 * @property-read Collection<int, AnswerSet> $answerSets
 */
class Form extends Model
{
    use UsesConfiguredTable;

    public const TABLE_CONFIG_KEY = 'forms';

    protected $fillable = [
        'key',
        'title',
        'meta',
        'is_active',
    ];

    protected $casts = [
        'meta' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Form $form) {
            if (! empty($form->key)) {
                return;
            }

            $base = Str::slug($form->title ?? 'form') ?: (string) Str::uuid();
            $key = $base;
            $suffix = 1;

            while ($form->newQuery()->where('key', $key)->exists()) {
                $key = $base.'_'.$suffix++;
            }

            $form->key = $key;
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(config('formbuilder.models.form_item'), 'form_id')->orderBy('position');
    }

    public function branchingRules(): HasMany
    {
        return $this->hasMany(config('formbuilder.models.branching'), 'form_id');
    }

    public function answerSets(): HasMany
    {
        return $this->hasMany(config('formbuilder.models.answer_set'), 'form_id');
    }
}
