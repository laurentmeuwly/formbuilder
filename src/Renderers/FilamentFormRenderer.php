<?php

namespace LaurentMeuwly\FormBuilder\Renderers;

use Filament\Forms;
use LaurentMeuwly\FormBuilder\Contracts\RendersForm;
use LaurentMeuwly\FormBuilder\Enums\FormFieldType;
use LaurentMeuwly\FormBuilder\Models\Form;

class FilamentFormRenderer implements RendersForm
{
    /**
     * Return Filament form components for every item of the form.
     *
     * Requires filament/forms (3.x, 4.x or 5.x).
     *
     * @param  array<string, mixed>  $context
     * @return list<Forms\Components\Field>
     */
    public function render(Form $form, array $context = []): array
    {
        $components = [];

        foreach ($form->items as $item) {
            if ($item->type === null) {
                continue;
            }

            $comp = match ($item->type) {
                FormFieldType::TEXT => Forms\Components\TextInput::make($item->key)
                    ->label($item->label),
                FormFieldType::TEXTAREA => Forms\Components\Textarea::make($item->key)
                    ->label($item->label),
                FormFieldType::NUMBER => Forms\Components\TextInput::make($item->key)
                    ->label($item->label)
                    ->numeric(),
                FormFieldType::SELECT => Forms\Components\Select::make($item->key)
                    ->label($item->label)
                    ->options(collect($item->options ?? [])->pluck('label', 'value')->all()),
                FormFieldType::RADIO => Forms\Components\Radio::make($item->key)
                    ->label($item->label)
                    ->options(collect($item->options ?? [])->pluck('label', 'value')->all()),
                FormFieldType::CHECKBOX => Forms\Components\CheckboxList::make($item->key)
                    ->label($item->label)
                    ->options(collect($item->options ?? [])->pluck('label', 'value')->all()),
                FormFieldType::DATE => Forms\Components\DatePicker::make($item->key)
                    ->label($item->label),
                FormFieldType::FILE => Forms\Components\FileUpload::make($item->key)
                    ->label($item->label),
            };

            $components[] = $comp->required($item->isRequired());
        }

        return $components;
    }
}
