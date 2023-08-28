<?php

namespace HusamTariq\FilamentDatabaseSchedule\Filament\Resources;

use Awcodes\FilamentTableRepeater\Components\TableRepeater;
use Carbon\Carbon;
use HusamTariq\FilamentDatabaseSchedule\Filament\Resources\ScheduleResource\Pages;
use HusamTariq\FilamentDatabaseSchedule\Models\Schedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Card;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;
use HusamTariq\FilamentDatabaseSchedule\Filament\Columns\ActionGroup;
use HusamTariq\FilamentDatabaseSchedule\Filament\Columns\ScheduleArguments;
use HusamTariq\FilamentDatabaseSchedule\Filament\Columns\ScheduleOptions;
use HusamTariq\FilamentDatabaseSchedule\Http\Services\CommandService;
use HusamTariq\FilamentDatabaseSchedule\Rules\Corn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class ScheduleResource extends Resource
{

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static Collection $commands;


    public static function getModel(): string
    {
        return  config('filament-database-schedule.model');
    }

    public static function getNavigationIcon(): ?string
    {
        return config('filament-database-schedule.navigation_icon');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-database-schedule::schedule.resource.plural');
    }

    public static function getModelLabel(): string
    {
        return __('filament-database-schedule::schedule.resource.single');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-database-schedule::schedule.resource.navigation');
    }

    public static function getSlug(): string
    {
        return config('filament-database-schedule.route_slug');
    }

    public static function form(Form $form): Form
    {
        static::$commands = CommandService::get();
        return $form
            ->schema([
                Card::make([
                    Forms\Components\Select::make('command')->label(__('filament-database-schedule::schedule.fields.command'))
                        ->options(
                            fn () =>
                            config('filament-database-schedule.commands.enable_custom') ?
                                static::$commands->pluck('full_name', 'name')->prepend(__('filament-database-schedule::schedule.messages.custom'), 'custom') : static::$commands->pluck('full_name', 'name')
                        )
                        ->reactive()
                        ->searchable()
                        ->required()
                        ->afterStateUpdated(function ($set, $state) {
                            $set('params', static::$commands->firstWhere('name', $state)['arguments'] ?? []);
                            $set('options_with_value', static::$commands->firstWhere('name', $state)['options']["withValue"] ?? []);
                        }),
                    Forms\Components\TextInput::make('command_custom')
                        ->placeholder(__('filament-database-schedule::schedule.messages.custom-command-here'))
                        ->label(__('filament-database-schedule::schedule.messages.custom'))
                        ->required()
                        ->visible(fn ($get) => $get('command') === 'custom' && config('filament-database-schedule.commands.enable_custom')),
                    TableRepeater::make('params')->label(__('filament-database-schedule::schedule.fields.arguments'))
                        ->schema([
                            Forms\Components\TextInput::make('value')->label(fn ($get) => ucfirst($get('name')))->required(fn ($get) => $get('required')),
                            Forms\Components\Hidden::make('name'),
                        ])->disableItemCreation()->withoutHeader()->disableItemDeletion()->disableItemMovement()
                        ->columnSpan('full')->visible(fn ($get) => !empty(static::$commands->firstWhere('name', $get('command'))['arguments'])),
                    TableRepeater::make('options_with_value')->label(__('filament-database-schedule::schedule.fields.options_with_value'))
                        ->schema([
                            Forms\Components\TextInput::make('value')->label(fn ($get) => ucfirst($get('name')))->required(fn ($get) => $get('required')),
                            Forms\Components\Hidden::make('type')->default('string'),
                            Forms\Components\Hidden::make('name'),
                        ])->disableItemCreation()->withoutHeader()->disableItemDeletion()->disableItemMovement()->default([])
                        ->columnSpan('full')->visible(fn ($state) => !empty($state)),
                    Forms\Components\CheckboxList::make('options')->label(__('filament-database-schedule::schedule.fields.options'))
                        ->options(
                            fn ($get) =>
                            collect(static::$commands->firstWhere('name', $get('command'))['options']['withoutValue'] ?? [])
                                ->mapWithKeys(function ($value) {
                                    return [$value => $value];
                                }),
                        )->columns(3)->columnSpanFull()->visible(fn (Forms\Components\CheckboxList $component) => !empty($component->getOptions())),
                    Forms\Components\TextInput::make('expression')
                        ->placeholder('* * * * *')
                        ->rules([new Corn()])
                        ->label(__('filament-database-schedule::schedule.fields.expression'))
                        ->required()->helperText(fn () => config('filament-database-schedule.tool-help-cron-expression.enable') ? new HtmlString(" <a href='" . config('filament-database-schedule.tool-help-cron-expression.url') . "' target='_blank'>" . __('filament-database-schedule::schedule.messages.help-cron-expression') . " </a>") : null),
                    Forms\Components\TagsInput::make('environments')
                        ->placeholder(null)
                        ->label(__('filament-database-schedule::schedule.fields.environments')),
                    Forms\Components\TextInput::make('log_filename')
                        ->label(__('filament-database-schedule::schedule.fields.log_filename'))
                        ->helperText(__('filament-database-schedule::schedule.messages.help-log-filename')),
                    Forms\Components\TextInput::make('webhook_before')
                        ->label(__('filament-database-schedule::schedule.fields.webhook_before')),
                    Forms\Components\TextInput::make('webhook_after')
                        ->label(__('filament-database-schedule::schedule.fields.webhook_after')),
                    Forms\Components\TextInput::make('email_output')
                        ->label(__('filament-database-schedule::schedule.fields.email_output')),
                    Forms\Components\Toggle::make('sendmail_success')
                        ->label(__('filament-database-schedule::schedule.fields.sendmail_success')),
                    Forms\Components\Toggle::make('sendmail_error')
                        ->label(__('filament-database-schedule::schedule.fields.sendmail_error')),
                    Forms\Components\Toggle::make('log_success')
                        ->label(__('filament-database-schedule::schedule.fields.log_success'))->default(true),
                    Forms\Components\Toggle::make('log_error')
                        ->label(__('filament-database-schedule::schedule.fields.log_error'))->default(true),
                    Forms\Components\Toggle::make('even_in_maintenance_mode')
                        ->label(__('filament-database-schedule::schedule.fields.even_in_maintenance_mode')),
                    Forms\Components\Toggle::make('without_overlapping')
                        ->label(__('filament-database-schedule::schedule.fields.without_overlapping')),
                    Forms\Components\Toggle::make('on_one_server')
                        ->label(__('filament-database-schedule::schedule.fields.on_one_server')),
                    Forms\Components\Toggle::make('run_in_background')
                        ->label(__('filament-database-schedule::schedule.fields.run_in_background')),
                ])->inlineLabel(false)
            ]);
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
                Tables\Columns\TextColumn::make('environments')->label(__('filament-database-schedule::schedule.fields.environments'))->separator(',')->searchable()->sortable()->badge()->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('status')->formatStateUsing(static function ($state): ?string {
                    switch ($state) {
                        case Schedule::STATUS_INACTIVE:
                            return __('filament-database-schedule::schedule.status.inactive');
                            break;

                        case Schedule::STATUS_TRASHED:
                            return  __('filament-database-schedule::schedule.status.trashed');
                            break;

                        case Schedule::STATUS_ACTIVE:
                            return  __('filament-database-schedule::schedule.status.active');
                            break;

                        default:
                            return $state;
                    }
                })->icons([
                        'heroicon-o-x',
                        'heroicon-o-document' => Schedule::STATUS_INACTIVE,
                        'heroicon-o-x-circle' => Schedule::STATUS_TRASHED,
                        'heroicon-o-check-circle' => Schedule::STATUS_ACTIVE,
                    ])
                    ->colors([
                        'warning' => Schedule::STATUS_INACTIVE,
                        'success' => Schedule::STATUS_ACTIVE,
                        'danger' => Schedule::STATUS_TRASHED,
                    ])->label(__('filament-database-schedule::schedule.fields.status'))->searchable()->sortable()->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('created_at')->label(__('filament-database-schedule::schedule.fields.created_at'))->searchable()->sortable()
                    ->dateTime()->wrap()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->getStateUsing(fn ($record) => $record->created_at == $record->updated_at ? __('filament-database-schedule::schedule.fields.never') : $record->updated_at)
                    ->wrap()->formatStateUsing(static function (Column $column, $state): ?string {
                        $format ??= Table::$defaultDateTimeDisplayFormat;

                        if (blank($state) || $state == __('filament-database-schedule::schedule.fields.never')) {
                            return $state;
                        }

                        return Carbon::parse($state)
                            ->setTimezone($timezone ?? $column->getTimezone())
                            ->translatedFormat($format);
                    })->label(__('filament-database-schedule::schedule.fields.updated_at'))->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),


            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()->hidden(fn ($record) => $record->trashed())->tooltip(__('filament-actions::edit.single.label')),
                    Tables\Actions\RestoreAction::make()->tooltip(__('filament-actions::restore.single.label')),
                    Tables\Actions\DeleteAction::make()->tooltip(__('filament-actions::delete.single.label')),
                    Tables\Actions\ForceDeleteAction::make()->tooltip(__('filament-support::actions/force-delete.single.label')),
                    Tables\Actions\Action::make('toggle')->disabled(fn ($record) => $record->trashed())
                        ->icon(fn ($record) => $record->status == Schedule::STATUS_ACTIVE ? 'schedule-pause-fill' : 'schedule-play-fill')->color(fn ($record) => $record->status == Schedule::STATUS_ACTIVE ? 'warning' : 'success')->action(function ($record): void {
                            if ($record->status == Schedule::STATUS_ACTIVE)
                                $record->status = Schedule::STATUS_INACTIVE;
                            else if ($record->status == Schedule::STATUS_INACTIVE)
                                $record->status = Schedule::STATUS_ACTIVE;
                            $record->save();
                        })->tooltip(fn ($record) => $record->status == Schedule::STATUS_ACTIVE ? __('filament-database-schedule::schedule.buttons.inactivate') : __('filament-database-schedule::schedule.buttons.activate')),
                    Tables\Actions\ViewAction::make()->icon('schedule-history')->color('gray')->tooltip(__('filament-database-schedule::schedule.buttons.history')),
                ])

            ])
            ->defaultPaginationPageOption(config('filament-database-schedule.per_page', 10) ?: config('tables.pagination.default_records_per_page'))
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])->defaultSort(config('filament-database-schedule.default_ordering'), config('filament-database-schedule.default_ordering_direction'));
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
