<?php

namespace HusamTariq\FilamentDatabaseSchedule\Filament\Columns;

use Filament\Tables\Columns\TextColumn;

class ScheduleOptions extends TextColumn
{

    protected bool $withValue = true;

    public function withValue(bool $withValue = true): static
    {
        $this->withValue = $withValue;

        return $this;
    }

    public function getTags(): array
    {
        if($this->withValue)
        return $this->record->getOptions();
        else{
            return parent::getTags();
        }
    }
}
