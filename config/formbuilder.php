<?php

use LaurentMeuwly\FormBuilder\Models\Answer;
use LaurentMeuwly\FormBuilder\Models\AnswerSet;
use LaurentMeuwly\FormBuilder\Models\BranchingRule;
use LaurentMeuwly\FormBuilder\Models\Form;
use LaurentMeuwly\FormBuilder\Models\FormItem;
use LaurentMeuwly\FormBuilder\Renderers\NullRenderer;

return [

    'branching' => [
        'enabled' => true,
    ],

    // Replaceable by the host application (ex: FilamentFormRenderer)
    'renderer' => NullRenderer::class,

    // Adaptable by the host application
    'table_names' => [
        'forms' => 'fb_forms',
        'form_items' => 'fb_form_items',
        'branching_rules' => 'fb_branching_rules',
        'answer_sets' => 'fb_answer_sets',
        'answers' => 'fb_answers',

    ],

    // Make the models overridable by the host application
    'models' => [
        'form' => Form::class,
        'form_item' => FormItem::class,
        'branching' => BranchingRule::class,
        'answer_set' => AnswerSet::class,
        'answer' => Answer::class,
    ],

];
