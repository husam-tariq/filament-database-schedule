@php
$actions=$getActions();
@endphp
<div style="display: grid; gap: 0.25rem; width: max-content; grid-template-columns: repeat(2, minmax(0, 1fr));">
@foreach ($actions as $action)
            @if (! $action->isHidden())
                {{ $action }}
            @endif
        @endforeach
</div>
