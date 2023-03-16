<?php

namespace HusamTariq\FilamentDatabaseSchedule\Filament\Resources\ScheduleResource\Pages;

use HusamTariq\FilamentDatabaseSchedule\Filament\Resources\ScheduleResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

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
