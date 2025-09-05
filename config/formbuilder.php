<?php

return [

    'branching' => [
        'enabled' => true,
    ],

    // Replaceable by the host application (ex: FilamentFormRenderer)
    'renderer' => LaurentMeuwly\FormBuilder\Renderers\NullRenderer::class,

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
        'form'          => \LaurentMeuwly\FormBuilder\Models\Form::class,
        'form_item'     => \LaurentMeuwly\FormBuilder\Models\FormItem::class,
        'branching'     => \LaurentMeuwly\FormBuilder\Models\BranchingRule::class,
        'answer_set'    => \LaurentMeuwly\FormBuilder\Models\AnswerSet::class,
        'answer'        => \LaurentMeuwly\FormBuilder\Models\Answer::class,
    ],

];