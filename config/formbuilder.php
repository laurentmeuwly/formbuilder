<?php

return [

    'branching' => [
        'enabled' => true,
    ],

    // Remplaçable par l'application hôte (ex: FilamentFormRenderer)
    'renderer' => LaurentMeuwly\FormBuilder\Renderers\NullRenderer::class,

    'table_names' => [
        'forms' => 'fb_forms',
        'form_items' => 'fb_form_items',
        'branching_rules' => 'fb_branching_rules',
        'answer_sets' => 'fb_answer_sets',
        'answers' => 'fb_answers',
        
    ],

];