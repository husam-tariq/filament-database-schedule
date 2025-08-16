<?php

namespace HusamTariq\FilamentDatabaseSchedule\Filament\Resources\Schedules\Pages;

use HusamTariq\FilamentDatabaseSchedule\Filament\Resources\Schedules\ScheduleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSchedules extends ListRecords
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
