<x-tables::actions.action
    :action="$action"
    component="filament-database-schedule::button"
    :outlined="$isOutlined()"
    :icon-position="$getIconPosition()"
    class="filament-tables-button-action"
>

    {{ $getLabel() }}
</x-tables::actions.action>
