<x-filament-database-schedule::icon-button
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)"
    :dark-mode="config('tables.dark_mode')"
>
    {{ $slot }}
</x-filament-database-schedule::icon-button>
