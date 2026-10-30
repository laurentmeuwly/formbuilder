<?php

namespace LaurentMeuwly\FormBuilder\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use LaurentMeuwly\FormBuilder\Traits\HasDynamicForm;

class Participant extends Model
{
    use HasDynamicForm;

    protected $guarded = [];
}
