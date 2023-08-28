
@php
$actions=$getActions();
@endphp
<div class="grid gap-1" style="width: max-content;grid-template-columns: repeat(2,minmax(0,1fr));">
@foreach ($actions as $action)
            @if (! $action->isHidden())
                {{ $action }}
            @endif
        @endforeach
</div>
