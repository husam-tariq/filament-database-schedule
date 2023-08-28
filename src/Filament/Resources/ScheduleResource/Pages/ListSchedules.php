<?php

namespace HusamTariq\FilamentDatabaseSchedule\Filament\Resources\ScheduleResource\Pages;

use Closure;
use HusamTariq\FilamentDatabaseSchedule\Filament\Resources\ScheduleResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListSchedules extends ListRecords
{
    protected static string $resource = ScheduleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }



    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (): ?string {
            return null;
        };
    }
}
