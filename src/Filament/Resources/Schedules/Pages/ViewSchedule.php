<?php

namespace HusamTariq\FilamentDatabaseSchedule\Filament\Resources\Schedules\Pages;

use HusamTariq\FilamentDatabaseSchedule\Filament\Resources\Schedules\ScheduleResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Resources\Concerns\HasTabs;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use HusamTariq\FilamentDatabaseSchedule\Models\ScheduleHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Url;

class ViewSchedule extends Page implements Tables\Contracts\HasTable
{
    use HasTabs;
    use Tables\Concerns\InteractsWithTable {
        makeTable as makeBaseTable;
    }

    #[Url(as: 'reordering')]
    public bool $isTableReordering = false;

    /**
     * @var array<string, mixed> | null
     */
    #[Url(as: 'filters')]
    public ?array $tableFilters = null;

    #[Url(as: 'grouping')]
    public ?string $tableGrouping = null;

    /**
     * @var ?string
     */
    #[Url(as: 'search')]
    public $tableSearch = '';

    #[Url(as: 'sort')]
    public ?string $tableSort = null;

    #[Url(as: 'tab')]
    public ?string $activeTab = null;

    public function getBreadcrumb(): ?string
    {
        return static::$breadcrumb ?? __('filament-panels::resources/pages/view-record.breadcrumb');
    }

    public function table(Table $table): Table
    {
        return $table->columns($this->getTableColumns());
    }
    protected static string $resource = ScheduleResource::class;


    use InteractsWithRecord;

    protected function getActions(): array
    {
        return [
            Action::make('clearHistory')
                ->label(__('filament-database-schedule::schedule.buttons.clear_history'))
                ->requiresConfirmation()
                ->modalHeading(__('filament-database-schedule::schedule.buttons.clear_history'))
                ->modalDescription('Are you sure you want to delete all entries for the selected schedule?')
                ->modalSubmitActionLabel('Yes, delete it all')
                ->modalIcon('heroicon-m-trash')
                ->visible($this->record->histories->count())
                ->color('danger')
                ->icon('heroicon-m-trash')
                ->action(function () {
                    $this->record->histories()->delete();
                }),
        ];
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




    protected function getTableQuery(): Builder
    {
        return ScheduleHistory::where('schedule_id', $this->record->id)->orderBy('created_at', 'desc');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getTabsContentComponent(),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE),
                EmbeddedTable::make(),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER),
            ]);
    }
    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\Layout\Split::make([
                Tables\Columns\TextColumn::make('command')->label(__('filament-database-schedule::schedule.fields.command')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament-database-schedule::schedule.fields.expression'))
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament-database-schedule::schedule.fields.expression'))
                    ->formatStateUsing(function ($state, $record) {
                        if ($state == $record->created_at) {
                            return "Processing...";
                        } else {
                            return $state->diffInSeconds($record->created_at) . " seconds";
                        }
                    }),
                Tables\Columns\TextColumn::make('output')
                    ->label("Output lines")
                    ->formatStateUsing(function ($state) {
                        return (count(explode("<br />", nl2br($state))) - 1) . " rows of output";
                    }),
            ]),
            Tables\Columns\Layout\Panel::make([

                Tables\Columns\TextColumn::make('output')->extraAttributes(["class" => "!max-w-max"], true)
                    ->formatStateUsing(function ($state) {
                        return new HtmlString(nl2br($state));
                    }),


            ])->collapsible()->collapsed(config("filament-database-schedule.history_collapsed")),
        ];
    }
}
