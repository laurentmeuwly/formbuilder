<?php

namespace LaurentMeuwly\FormBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LaurentMeuwly\FormBuilder\Traits\UsesConfiguredTable;

class BranchingRule extends Model
{
    use UsesConfiguredTable;

    public const TABLE_CONFIG_KEY = 'branching_rules';    

    protected $fillable = [
        'form_id',
        'condition', // JSON: {"if": {"field":"sample_type","op":"=","value":"solid"}, "then":{"show":["granulometry"],"hide":["ph"]} }
        'meta',
    ];

    protected $casts = [
        'condition' => 'array',
        'meta' => 'array',
    ];

    public function form(): BelongsTo
    {
        $formClass = config('formbuilder.models.form');
        return $this->belongsTo($formClass);
    }
}