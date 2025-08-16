<?php

namespace HusamTariq\FilamentDatabaseSchedule\Filament\Resources\Schedules\Pages;

use HusamTariq\FilamentDatabaseSchedule\Filament\Resources\Schedules\ScheduleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;
use Filament\Notifications\Notification;

class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function onValidationError(ValidationException $exception): void
    {
        Notification::make()
            ->title($exception->getMessage())
            ->danger()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
