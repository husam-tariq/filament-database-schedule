<?php

namespace HusamTariq\FilamentDatabaseSchedule\Filament\Resources;

use Carbon\Carbon;
use HusamTariq\FilamentDatabaseSchedule\Filament\Resources\ScheduleResource\Pages;
use HusamTariq\FilamentDatabaseSchedule\Models\Schedule;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\Column;
use HusamTariq\FilamentDatabaseSchedule\Filament\Columns\ActionGroup;
use HusamTariq\FilamentDatabaseSchedule\Filament\Columns\ScheduleArguments;
use HusamTariq\FilamentDatabaseSchedule\Filament\Columns\ScheduleOptions;
use HusamTariq\FilamentDatabaseSchedule\Http\Services\CommandService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScheduleResource extends Resource
{

    protected static ?string $navigationIcon = 'heroicon-o-collection';


    public static function getModel(): string
    {
        return  config('filament-database-schedule.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-database-schedule::schedule.resource.plural');
    }

    public static function getModelLabel(): string
    {
        return __('filament-database-schedule::schedule.resource.single');
    }

    protected static function getNavigationGroup(): ?string
    {
        return __('filament-database-schedule::schedule.resource.navigation');
    }

    public static function getSlug(): string
    {
            return config('filament-database-schedule.route_slug');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('command')->getStateUsing(function ($record) {
                    if ($record->command == 'custom')
                        return $record->command_custom;
                    return $record->command;
                })->label(__('filament-database-schedule::schedule.fields.command'))->searchable()->sortable(),
                ScheduleArguments::make('params')->label(__('filament-database-schedule::schedule.fields.arguments'))->searchable()->sortable(),
                ScheduleOptions::make('options')->label(__('filament-database-schedule::schedule.fields.options'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('expression')->label(__('filament-database-schedule::schedule.fields.expression'))->searchable()->sortable(),
                Tables\Columns\TagsColumn::make('environments')->label(__('filament-database-schedule::schedule.fields.environments'))->separator(',')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label(__('filament-database-schedule::schedule.fields.created_at'))->searchable()->sortable()
                    ->dateTime()->wrap(),
                Tables\Columns\TextColumn::make('updated_at')->getStateUsing(fn ($record) => $record->created_at == $record->updated_at ? __('filament-database-schedule::schedule.fields.never') : $record->updated_at)
                    ->wrap()->formatStateUsing(static function (Column $column, $state): ?string {
                        $format ??= config('tables.date_time_format');
                        if (blank($state) || $state == __('filament-database-schedule::schedule.fields.never')) {
                            return $state;
                        }

                        return Carbon::parse($state)
                            ->setTimezone($timezone ?? $column->getTimezone())
                            ->translatedFormat($format);
                    })->label(__('filament-database-schedule::schedule.fields.updated_at'))->searchable()->sortable(),
                Tables\Columns\BadgeColumn::make('status')->enum([
                    Schedule::STATUS_INACTIVE => __('filament-database-schedule::schedule.status.inactive'),
                    Schedule::STATUS_TRASHED => __('filament-database-schedule::schedule.status.trashed'),
                    Schedule::STATUS_ACTIVE => __('filament-database-schedule::schedule.status.active'),
                ])->icons([
                    'heroicon-o-x',
                    'heroicon-o-document' => Schedule::STATUS_INACTIVE,
                    'heroicon-o-x-circle' => Schedule::STATUS_TRASHED,
                    'heroicon-o-check-circle' => Schedule::STATUS_ACTIVE,
                ])
                    ->colors([
                        'warning' => Schedule::STATUS_INACTIVE,
                        'success' => Schedule::STATUS_ACTIVE,
                        'danger' => Schedule::STATUS_TRASHED,
                    ])->label(__('filament-database-schedule::schedule.fields.status'))->searchable()->sortable()

            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()->hidden(fn($record)=>$record->trashed())->tooltip(__('filament-support::actions/edit.single.label')),
                    Tables\Actions\RestoreAction::make()->tooltip(__('filament-support::actions/restore.single.label')),
                    Tables\Actions\DeleteAction::make()->tooltip(__('filament-support::actions/delete.single.label')),
                    Tables\Actions\ForceDeleteAction::make()->tooltip(__('filament-support::actions/force-delete.single.label')),
                    Tables\Actions\Action::make('toggle')->disabled(fn($record)=>$record->trashed())
                    ->icon(fn ($record) => $record->status == Schedule::STATUS_ACTIVE ? 'schedule-pause-fill' : 'schedule-play-fill')->color(fn ($record) => $record->status == Schedule::STATUS_ACTIVE ? 'warning' : 'success')->action(function ($record): void {
                        if ($record->status == Schedule::STATUS_ACTIVE)
                            $record->status = Schedule::STATUS_INACTIVE;
                        else if ($record->status == Schedule::STATUS_INACTIVE)
                            $record->status = Schedule::STATUS_ACTIVE;
                        $record->save();
                    })->tooltip(fn ($record) => $record->status == Schedule::STATUS_ACTIVE ? __('filament-database-schedule::schedule.buttons.inactivate') : __('filament-database-schedule::schedule.buttons.activate')),
                    Tables\Actions\ViewAction::make()->icon('schedule-history')->color('gray')->tooltip(__('filament-database-schedule::schedule.buttons.history')) ,
                ])

            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])->defaultSort(config('filament-database-schedule.default_ordering'),config('filament-database-schedule.default_ordering_direction'))
           ;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
            'view' => Pages\ViewSchedule::route('/{record}'),
        ];
    }
}
