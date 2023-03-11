<?php

namespace HusamTariq\FilamentDatabaseSchedule\Filament\Resources\ScheduleResource\Pages;

use HusamTariq\FilamentDatabaseSchedule\Filament\Resources\ScheduleResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\Concerns\HasRecordBreadcrumb;
use Filament\Resources\Pages\Concerns\HasRelationManagers;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables;
use HusamTariq\FilamentDatabaseSchedule\Filament\Columns\ScheduleArguments;
use HusamTariq\FilamentDatabaseSchedule\Filament\Columns\ScheduleOptions;
use HusamTariq\FilamentDatabaseSchedule\Models\ScheduleHistory;

class ViewSchedule extends Page implements HasTable
{

    protected static string $resource = ScheduleResource::class;
    protected static string $view = 'filament::resources.pages.list-records';
    use InteractsWithRecord;
    use HasRecordBreadcrumb;
    use HasRelationManagers;
    use InteractsWithTable;

    protected function getActions(): array
    {
        return [];
    }

    protected function getTitle(): string
    {
        return __('filament-database-schedule::schedule.resource.history');
    }

    public function mount($record): void
    {
        static::authorizeResourceAccess();

        $this->record = $this->resolveRecord($record);

        abort_unless(static::getResource()::canView($this->getRecord()), 403);
    }
    protected function getRelationManagers(): array
    {
        return [];
    }


    protected function getTableQuery(): Builder
    {
        return ScheduleHistory::where('schedule_id',$this->record->id)->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\Layout\Split::make([
                Tables\Columns\TextColumn::make('command')->label(__('filament-database-schedule::schedule.fields.command')),
            ScheduleArguments::make('params')->withValue(false)->label(__('filament-database-schedule::schedule.fields.arguments')),
            ScheduleOptions::make('options')->withValue(false)->label(__('filament-database-schedule::schedule.fields.options')),
            Tables\Columns\TextColumn::make('created_at')->label(__('filament-database-schedule::schedule.fields.expression'))
            ->dateTime(),

            ]),Tables\Columns\Layout\Panel::make([

                Tables\Columns\TextColumn::make('output'),


            ])->collapsible(),

        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }
    /*  protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->context('view')
                ->disabled()
                ,
        ];
    } */
}
