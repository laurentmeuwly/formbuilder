# FormBuilder

[![Tests](https://github.com/laurentmeuwly/formbuilder/actions/workflows/tests.yml/badge.svg)](https://github.com/laurentmeuwly/formbuilder/actions/workflows/tests.yml)
[![Latest Tag](https://img.shields.io/github/v/tag/laurentmeuwly/formbuilder?sort=semver)](https://github.com/laurentmeuwly/formbuilder/tags)
![Laravel](https://img.shields.io/badge/Laravel-12%20|%2013-red)
![PHP](https://img.shields.io/badge/PHP-8.2%20|%208.3%20|%208.4-blue)
![License](https://img.shields.io/badge/license-MIT-green)

FormBuilder stores **dynamic forms in the database** and gives your Laravel application everything around them: field definitions, conditional branching (show / hide questions depending on previous answers), Laravel validation rules and answer storage attached to any of your models.

It is UI-agnostic. A ready-made renderer turns a form into Filament components, and you can plug in your own renderer for Livewire, Blade, Inertia or an API.

## Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Concepts](#concepts)
- [Defining a form](#defining-a-form)
- [Branching rules](#branching-rules)
- [Validation](#validation)
- [Rendering](#rendering)
- [Storing answers](#storing-answers)
- [Configuration and extension](#configuration-and-extension)
- [Upgrading](#upgrading)
- [Development](#development)

## Requirements

| FormBuilder | Laravel | PHP       | Filament (optional) |
|-------------|---------|-----------|---------------------|
| 1.2.x       | 12, 13  | 8.2 – 8.4 | 3, 4, 5             |
| 1.1.x       | 11, 12  | 8.2+      | 3                   |

Laravel 13 itself requires PHP 8.3 or later; Composer picks the right combination for you.

## Installation

FormBuilder is not published on Packagist. Declare the GitHub repository in your application's `composer.json`:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/laurentmeuwly/formbuilder"
    }
]
```

then require it:

```bash
composer require laurentmeuwly/formbuilder:^1.2
```

Publish the configuration and the migrations, then migrate:

```bash
php artisan vendor:publish --tag=formbuilder-config
php artisan vendor:publish --tag=formbuilder-migrations
php artisan migrate
```

Table names come from the configuration (prefix `fb_` by default): if you want other names, publish and edit the configuration **before** migrating.

## Concepts

| Model           | Table (default)      | Role                                                      |
|-----------------|----------------------|-----------------------------------------------------------|
| `Form`          | `fb_forms`           | A questionnaire, identified by a unique `key`             |
| `FormItem`      | `fb_form_items`      | A question: key, label, type, options, validation         |
| `BranchingRule` | `fb_branching_rules` | "If answer X … then show / hide questions Y, Z"           |
| `AnswerSet`     | `fb_answer_sets`     | One filling of a form, attached to any model (morph)      |
| `Answer`        | `fb_answers`         | The value given to one question, referenced by its key    |

Answers reference questions by their **key** (`field_key`), not by their id: keep item keys stable once a form is in use.

Field types (`LaurentMeuwly\FormBuilder\Enums\FormFieldType`): `text`, `textarea`, `number`, `select`, `radio`, `checkbox` (multiple choice), `date`, `file`.

## Defining a form

```php
use LaurentMeuwly\FormBuilder\Enums\FormFieldType;
use LaurentMeuwly\FormBuilder\Models\Form;

$form = Form::create([
    'key' => 'sample_intake_v1',          // optional: generated from the title
    'title' => 'Sample intake',
]);

$form->items()->createMany([
    [
        'key' => 'sample_type',
        'label' => 'Sample type',
        'type' => FormFieldType::SELECT,
        'position' => 1,
        'is_required' => true,
        'options' => [
            ['value' => 'solid', 'label' => 'Solid'],
            ['value' => 'liquid', 'label' => 'Liquid'],
        ],
    ],
    [
        'key' => 'granulometry',
        'label' => 'Granulometry (µm)',
        'type' => FormFieldType::NUMBER,
        'position' => 2,
        'validation' => ['min' => 0, 'max' => 5000],
        'meta' => ['unit' => 'µm'],
    ],
]);
```

| Attribute     | Notes                                                                       |
|---------------|-----------------------------------------------------------------------------|
| `key`         | Unique within the form. A UUID is generated when empty.                     |
| `type`        | A `FormFieldType`. Required.                                                |
| `position`    | Display order (`$form->items` is sorted by position).                       |
| `is_required` | Simple required flag.                                                       |
| `options`     | For select / radio / checkbox: a list of `['value' => …, 'label' => …]`.    |
| `validation`  | `required` (bool), `min`, `max`, `regex`, `in` (list of allowed values).    |
| `meta`        | Free JSON for your UI: help text, placeholder, unit, …                      |

## Branching rules

A rule is stored in the `condition` column using this canonical format:

```php
$form->branchingRules()->create([
    'condition' => [
        'if' => ['field' => 'sample_type', 'op' => '=', 'value' => 'solid'],
        'then' => [
            'show' => ['granulometry'],
            'hide' => ['ph'],
        ],
    ],
]);
```

Compute the visible questions for the answers given so far:

```php
use LaurentMeuwly\FormBuilder\Support\BranchingEvaluator;

$visible = BranchingEvaluator::visibleKeys($form, ['sample_type' => 'solid']);
// ['sample_type', 'granulometry']
```

Semantics:

- A question targeted by at least one `show` is **hidden by default** and appears only when a matching rule shows it.
- `hide` always wins over `show`, whatever the order of the rules.
- Malformed rules (no `if`, no `field` or `op`) are ignored. A missing answer is compared as `null`.

| Operator          | True when …                                                                |
|-------------------|----------------------------------------------------------------------------|
| `=` / `==`        | answer equals the value (loose comparison: `"5"` equals `5`)               |
| `!=` / `<>`       | answer differs from the value                                              |
| `>` `<` `>=` `<=` | both are numeric and the comparison holds                                  |
| `in`              | checkbox answer contains the value · answer is one of a list of values · text answer contains the value |

## Validation

`ValidationRuleBuilder` turns the items into Laravel rules, keyed by item key:

```php
use LaurentMeuwly\FormBuilder\Support\ValidationRuleBuilder;

ValidationRuleBuilder::for($form);
// [
//     'sample_type'  => ['required'],
//     'granulometry' => ['nullable', 'numeric', 'min:0', 'max:5000'],
// ]
```

An item is required when its `is_required` column is true **or** its `validation.required` is `true`. Number, date and checkbox items get a `numeric`, `date` or `array` rule, so `min` / `max` compare values (or counts) rather than string lengths.

The builder returns rules for **all** items. Validate only the visible ones, otherwise a required but hidden question would block the submission:

```php
use Illuminate\Support\Arr;

$visible = BranchingEvaluator::visibleKeys($form, $request->all());
$rules = Arr::only(ValidationRuleBuilder::for($form), $visible);

$data = $request->validate($rules);
```

## Rendering

The configured renderer is bound to `LaurentMeuwly\FormBuilder\Contracts\RendersForm` and exposed by the `FormBuilder` facade.

**Default: `NullRenderer`** returns plain arrays (`['form' => …, 'items' => […]]`), handy for APIs, Blade or Inertia.

**Filament:** install `filament/forms` (3, 4 or 5) and set the renderer:

```php
// config/formbuilder.php
'renderer' => \LaurentMeuwly\FormBuilder\Renderers\FilamentFormRenderer::class,
```

```php
use LaurentMeuwly\FormBuilder\Facades\FormBuilder;

$schema = FormBuilder::render($form->load('items'));   // list of Filament form components
```

Each item becomes the matching component (`TextInput`, `Textarea`, `Select`, `Radio`, `CheckboxList`, `DatePicker`, `FileUpload`) with its label, options and required flag. Branching is not wired into the components: use `BranchingEvaluator` (for instance in `->visible()` closures) if your form must react live.

**Your own renderer:** implement `RendersForm::render(Form $form, array $context = []): mixed` and set it in the configuration. A class that does not implement the contract throws an `InvalidArgumentException`.

## Storing answers

Add the `HasDynamicForm` trait to the model that answers forms:

```php
use LaurentMeuwly\FormBuilder\Traits\HasDynamicForm;

class Order extends Model
{
    use HasDynamicForm;
}
```

```php
$set = $order->startAnswerSet($form, ['source' => 'web']);   // meta is optional

foreach ($data as $key => $value) {
    $set->answers()->create(['field_key' => $key, 'value' => $value]);
}

$set->update(['submitted_at' => now()]);

$order->formAnswerSets;               // all answer sets of this order
$set->answers->pluck('value', 'field_key');
```

`value` is stored as JSON: strings, numbers, lists (checkboxes) and `null` are returned with their original type.

## Configuration and extension

`config/formbuilder.php`:

```php
return [
    'branching' => ['enabled' => true],

    'renderer' => \LaurentMeuwly\FormBuilder\Renderers\NullRenderer::class,

    'table_names' => [
        'forms' => 'fb_forms',
        'form_items' => 'fb_form_items',
        'branching_rules' => 'fb_branching_rules',
        'answer_sets' => 'fb_answer_sets',
        'answers' => 'fb_answers',
    ],

    'models' => [
        'form' => \LaurentMeuwly\FormBuilder\Models\Form::class,
        'form_item' => \LaurentMeuwly\FormBuilder\Models\FormItem::class,
        'branching' => \LaurentMeuwly\FormBuilder\Models\BranchingRule::class,
        'answer_set' => \LaurentMeuwly\FormBuilder\Models\AnswerSet::class,
        'answer' => \LaurentMeuwly\FormBuilder\Models\Answer::class,
    ],
];
```

Replace any model with a subclass to add relations, scopes or casts; the package relations and `HasDynamicForm` use the configured classes. Table names always come from `table_names`, even for subclasses.

## Upgrading

See [CHANGELOG.md](CHANGELOG.md) for details.

**1.1 → 1.2** — requires Laravel 12 or 13. Run `composer update laurentmeuwly/formbuilder`; no migration or configuration change is needed. Review two behaviour fixes: items flagged `is_required` are now required in validation and in Filament, and number / date / checkbox items now get a type rule.

## Development

```bash
composer install
composer test        # Pest
composer analyse     # PHPStan / Larastan
composer lint        # Pint (check only)
composer format      # Pint (fix)
```

The GitHub Actions workflow runs the test suite on every supported PHP × Laravel combination, plus static analysis, code style and `composer audit`.

## License

The MIT License (MIT). Please see the [License File](LICENSE.md) for more information.
