<?php

namespace HusamTariq\FilamentDatabaseSchedule\Filament\Resources\Schedules\Pages;

use HusamTariq\FilamentDatabaseSchedule\Filament\Resources\Schedules\ScheduleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSchedule extends EditRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->icon('schedule-history')
                ->label(__('filament-database-schedule::schedule.buttons.history'))
                ->visible(fn($record) => $record->histories()->count()),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
