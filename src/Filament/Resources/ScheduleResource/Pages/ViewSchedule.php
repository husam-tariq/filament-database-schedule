<?php

namespace HusamTariq\FilamentDatabaseSchedule\Filament\Resources\ScheduleResource\Pages;

use HusamTariq\FilamentDatabaseSchedule\Filament\Resources\ScheduleResource;
use Filament\Tables;
use Filament\Forms;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\HasRelationManagers;
use Filament\Resources\Concerns\HasTabs;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Livewire\Attributes\Url;

use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use HusamTariq\FilamentDatabaseSchedule\Filament\Columns\ScheduleArguments;
use HusamTariq\FilamentDatabaseSchedule\Filament\Columns\ScheduleOptions;
use HusamTariq\FilamentDatabaseSchedule\Models\ScheduleHistory;

class ViewSchedule extends Page implements HasTable
{

    protected static string $resource = ScheduleResource::class;
    protected static string $view = 'filament-panels::resources.pages.list-records';
    use InteractsWithRecord;
    use HasRelationManagers;
    use HasTabs;

    #[Url]
    public ?string $activeTab = null;
   
    use Forms\Concerns\InteractsWithForms;
    use Tables\Concerns\InteractsWithTable {
        makeTable as makeBaseTable;
    }
    use Tables\Concerns\InteractsWithTable {
        makeTable as makeBaseTable;
    }
    protected function getActions(): array
    {
        return [];
    }

    public function getTitle(): string
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
        return ScheduleHistory::where('schedule_id', $this->record->id)->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\Layout\Split::make([
                Tables\Columns\TextColumn::make('command')->label(__('filament-database-schedule::schedule.fields.command')),
                // ScheduleArguments::make('params')->withValue(false)->label(__('filament-database-schedule::schedule.fields.arguments'))->separator(',')->badge(),
                Tables\Columns\TextColumn::make('options')->label(__('filament-database-schedule::schedule.fields.options'))->separator(',')->badge(),
                Tables\Columns\TextColumn::make('created_at')->label(__('filament-database-schedule::schedule.fields.expression'))
                    ->dateTime(),
            ]), Tables\Columns\Layout\Panel::make([

                Tables\Columns\TextColumn::make('output')->extraAttributes(["class"=>"!max-w-max"],true),


            ])->collapsible()->collapsed(config("filament-database-schedule.history_collapsed")),

        ];
    }
}
