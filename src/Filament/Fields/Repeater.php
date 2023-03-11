<?php

namespace HusamTariq\FilamentDatabaseSchedule\Filament\Fields;

use Filament\Forms\Components\Repeater as ComponentsRepeater;

class Repeater extends ComponentsRepeater
{

    public function getItemLabel(string $uuid): ?string
    {
        return $this->evaluate($this->itemLabel, [
            'state' => $this->getChildComponentContainer($uuid)->getRawState(),
            'uuid' => $uuid,
        ]);
    }
}
