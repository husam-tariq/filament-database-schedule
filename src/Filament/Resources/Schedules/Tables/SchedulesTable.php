<?php

namespace HusamTariq\FilamentDatabaseSchedule\Filament\Resources\Schedules\Tables;

use HusamTariq\FilamentDatabaseSchedule\Enums\Status;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('command')->getStateUsing(function ($record) {
                    if ($record->command == 'custom')
                        return $record->command_custom;
                    return $record->command;
                })->label(__('filament-database-schedule::schedule.fields.command'))->searchable()->sortable(),
                TextColumn::make('params')->label(__('filament-database-schedule::schedule.fields.arguments'))
                    ->getStateUsing(function ($record, $component) {
                        $tags = $record->params;
                        if (is_array($tags)) {
                            return collect($tags)->filter(fn($value) => !empty($value['value']))->map(fn($value, $key) => ($value['name'] ?? $key) . '=' . $value['value'])->toArray();
                        }

                        if (!($separator = $component->getSeparator())) {
                            return [];
                        }

                        $tags = explode($separator, $tags);

                        if (count($tags) === 1 && blank($tags[0])) {
                            $tags = [];
                        }
                        return $tags;
                    })->separator(",")->searchable()->sortable(),
                TextColumn::make('options')->label(__('filament-database-schedule::schedule.fields.options'))->searchable()->sortable()->getStateUsing(fn($record) => $record->getOptions())->separator(',')->badge(),
                TextColumn::make('expression')->label(__('filament-database-schedule::schedule.fields.expression'))->searchable()->sortable(),
                TextColumn::make('environments')->label(__('filament-database-schedule::schedule.fields.environments'))->separator(',')->searchable()->sortable()->badge()->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('status')
                    ->label(__('filament-database-schedule::schedule.fields.status'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(true, isToggledHiddenByDefault: false),
                TextColumn::make('created_at')->label(__('filament-database-schedule::schedule.fields.created_at'))->searchable()->sortable()
                    ->dateTime()->wrap()->toggleable(true, isToggledHiddenByDefault: false),
                TextColumn::make('updated_at')->getStateUsing(fn($record) => $record->created_at == $record->updated_at ? __('filament-database-schedule::schedule.fields.never') : $record->updated_at)
                    ->wrap()->formatStateUsing(static function ($column, $state) use ($table): ?string {
                        $format = $table->getDefaultDateTimeDisplayFormat();
                        if (blank($state) || $state == __('filament-database-schedule::schedule.fields.never')) {
                            return $state;
                        }
                        return Carbon::parse($state)
                            ->setTimezone($timezone ?? $column->getTimezone())
                            ->translatedFormat($format);
                    })->label(__('filament-database-schedule::schedule.fields.updated_at'))->searchable()->sortable()->toggleable(true, isToggledHiddenByDefault: false)
                ,

            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()->hidden(fn($record) => $record->trashed())->tooltip(__('filament-actions::edit.single.label'))->button()->hiddenLabel(),
                    RestoreAction::make()->tooltip(__('filament-actions::restore.single.label'))->button()->hiddenLabel(),
                    DeleteAction::make()->tooltip(__('filament-actions::delete.single.label'))->button()->hiddenLabel(),
                    ForceDeleteAction::make()->tooltip(__('filament-support::actions/force-delete.single.label'))->button()->hiddenLabel(),
                    Action::make('toggle')->button()->hiddenLabel()
                        ->disabled(fn($record) => $record->trashed())
                        ->icon(fn($record) => $record->status === Status::Active ? "heroicon-s-pause" : "heroicon-s-play")
                        ->color(fn($record) => $record->status === Status::Active ? 'warning' : 'success')
                        ->action(function ($record): void {
                            if ($record->status === Status::Active)
                                $record->status = Status::Inactive;
                            else if ($record->status === Status::Inactive)
                                $record->status = Status::Active;
                            $record->save();
                        })->tooltip(fn($record) => $record->status === Status::Active ? __('filament-database-schedule::schedule.buttons.inactivate') : __('filament-database-schedule::schedule.buttons.activate')),
                    ViewAction::make()->button()->hiddenLabel()
                        ->icon('schedule-history')
                        ->color('gray')
                        ->tooltip(__('filament-database-schedule::schedule.buttons.history'))
                        ->visible(fn($record) => $record->histories()->count()),
                ])->view('filament-database-schedule::components.action-group'),
            ])->defaultPaginationPageOption(config('filament-database-schedule.per_page', 10) ?: config('tables.pagination.default_records_per_page'))
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])->defaultSort(config('filament-database-schedule.default_ordering'), config('filament-database-schedule.default_ordering_direction'));
    }
}
