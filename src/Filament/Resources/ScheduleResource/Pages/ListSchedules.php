<?php

namespace HusamTariq\FilamentDatabaseSchedule\Filament\Resources\ScheduleResource\Pages;

use Closure;
use HusamTariq\FilamentDatabaseSchedule\Filament\Resources\ScheduleResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSchedules extends ListRecords
{
    protected static string $resource = ScheduleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

   /*  protected function getTableRecordsPerPage(): int
    {
        return (int) config('filament-database-schedule.per_page',10);
    }
 */
    protected function getTableRecordsPerPageSelectOptions(): array
    {
        $records_per_page_select_options = parent::getTableRecordsPerPageSelectOptions();
        $per_page = config('filament-database-schedule.per_page',10);
        if(!in_array($per_page,$records_per_page_select_options))
        array_unshift($records_per_page_select_options,$per_page);
        return $records_per_page_select_options;
    }

    protected function getDefaultTableRecordsPerPageSelectOption(): int
    {
        $perPage = session()->get(
            $this->getTablePerPageSessionKey(),
            config('filament-database-schedule.per_page',10) ?: config('tables.pagination.default_records_per_page'),
        );

        if (in_array($perPage, $this->getTableRecordsPerPageSelectOptions())) {
            return $perPage;
        }

        session()->remove($this->getTablePerPageSessionKey());

        return $this->getTableRecordsPerPageSelectOptions()[0];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (): ?string {
            return null;
        };
    }
}
