<?php

namespace LaurentMeuwly\FormBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchingRule extends Model
{
    protected $table = 'fb_branching_rules';

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
        return $this->belongsTo(Form::class);
    }
}