<?php

namespace LaurentMeuwly\FormBuilder\Renderers;

use Filament\Forms;
use LaurentMeuwly\FormBuilder\Contracts\RendersForm;
use LaurentMeuwly\FormBuilder\Enums\ItemType;
use LaurentMeuwly\FormBuilder\Models\Form;

class FilamentFormRenderer implements RendersForm
{
    public function render(Form $form, array $context = []): array
    {
        $components = [];

        foreach ($form->items as $item) {
            $comp = match ($item->type) {
                ItemType::TEXT => Forms\Components\TextInput::make($item->key)
                    ->label($item->label)
                    ->required(($item->validation['required'] ?? false) === true),
                ItemType::TEXTAREA => Forms\Components\Textarea::make($item->key)
                    ->label($item->label),
                ItemType::NUMBER => Forms\Components\TextInput::make($item->key)
                    ->label($item->label)
                    ->numeric(),
                ItemType::SELECT => Forms\Components\Select::make($item->key)
                    ->label($item->label)
                    ->options(collect($item->options ?? [])->pluck('label','value')->all()),
                ItemType::RADIO => Forms\Components\Radio::make($item->key)
                    ->label($item->label)
                    ->options(collect($item->options ?? [])->pluck('label','value')->all()),
                ItemType::CHECKBOX => Forms\Components\CheckboxList::make($item->key)
                    ->label($item->label)
                    ->options(collect($item->options ?? [])->pluck('label','value')->all()),
                ItemType::DATE => Forms\Components\DatePicker::make($item->key)
                    ->label($item->label),
                ItemType::FILE => Forms\Components\FileUpload::make($item->key)
                    ->label($item->label),
            };

            $components[] = $comp;
        }

        return $components;
    }
}