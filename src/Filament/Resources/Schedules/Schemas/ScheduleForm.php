<?php

namespace HusamTariq\FilamentDatabaseSchedule\Filament\Resources\Schedules\Schemas;

use HusamTariq\FilamentDatabaseSchedule\Http\Services\CommandService;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use HusamTariq\FilamentDatabaseSchedule\Rules\Corn;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class ScheduleForm
{
    public static Collection $commands;

    public static function configure(Schema $schema): Schema
    {
        static::$commands = CommandService::get();

        return $schema
            ->components([
                Select::make('command')->label(__('filament-database-schedule::schedule.fields.command'))
                    ->options(
                        fn() =>
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
                TextInput::make('command_custom')
                    ->placeholder(__('filament-database-schedule::schedule.messages.custom-command-here'))
                    ->label(__('filament-database-schedule::schedule.messages.custom'))
                    ->required()
                    ->visible(fn($get) => $get('command') === 'custom' && config('filament-database-schedule.commands.enable_custom')),
                Repeater::make('params')->label(__('filament-database-schedule::schedule.fields.arguments'))
                    ->schema([
                        TextInput::make('value')->prefix(fn($get) => ucfirst($get('name')))->required(fn($get) => $get('required'))->hiddenLabel(),
                        Hidden::make('name'),
                    ])->addable(false)->deletable(false)->reorderable(false)
                    ->visible(fn($get) => !empty(static::$commands->firstWhere('name', $get('command'))['arguments'])),
                Repeater::make('options_with_value')->label(__('filament-database-schedule::schedule.fields.options_with_value'))
                    ->schema([
                        TextInput::make('value')->prefix(fn($get) => ucfirst($get('name')))->required(fn($get) => $get('required'))->hiddenLabel(),
                        Hidden::make('type')->default('string'),
                        Hidden::make('name'),
                    ])->addable(false)->deletable(false)->reorderable(false)
                    ->visible(function ($state) {
                        $items = collect($state);
                        if ($items->isEmpty()) {
                            return false;
                        }
                        $first = $items->first();
                        $value = is_array($first) ? ($first['value'] ?? null) : ($first->value ?? null);

                        return !empty($value);
                    }),
                CheckboxList::make('options')->label(__('filament-database-schedule::schedule.fields.options'))
                    ->options(
                        fn($get) =>
                        collect(static::$commands->firstWhere('name', $get('command'))['options']['withoutValue'] ?? [])
                            ->mapWithKeys(function ($value) {
                                return [$value => $value];
                            }),
                    )->columns(3)->columnSpanFull()->visible(fn(CheckboxList $component) => !empty($component->getOptions())),
                TextInput::make('expression')
                    ->placeholder('* * * * *')
                    ->rules([new Corn()])
                    ->label(__('filament-database-schedule::schedule.fields.expression'))
                    ->required()->helperText(fn() => config('filament-database-schedule.tool-help-cron-expression.enable') ? new HtmlString(" <a href='" . config('filament-database-schedule.tool-help-cron-expression.url') . "' target='_blank'>" . __('filament-database-schedule::schedule.messages.help-cron-expression') . " </a>") : null),
                TagsInput::make('environments')
                    ->placeholder(null)
                    ->label(__('filament-database-schedule::schedule.fields.environments')),
                TextInput::make('log_filename')
                    ->label(__('filament-database-schedule::schedule.fields.log_filename'))
                    ->helperText(__('filament-database-schedule::schedule.messages.help-log-filename')),
                TextInput::make('webhook_before')
                    ->label(__('filament-database-schedule::schedule.fields.webhook_before')),
                TextInput::make('webhook_after')
                    ->label(__('filament-database-schedule::schedule.fields.webhook_after')),
                TextInput::make('email_output')
                    ->label(__('filament-database-schedule::schedule.fields.email_output')),
                Section::make('History')
                    ->label(__('filament-database-schedule::schedule.buttons.history'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('log_success')
                            ->label(__('filament-database-schedule::schedule.fields.log_success'))->default(true),
                        Toggle::make('log_error')
                            ->label(__('filament-database-schedule::schedule.fields.log_error'))->default(true),
                        Toggle::make('limit_history_count')
                            ->label(__('filament-database-schedule::schedule.fields.limit_history_count'))
                            ->live(),
                        TextInput::make('max_history_count')
                            ->label('')
                            ->numeric()
                            ->default(10)
                            ->visible(fn($get): bool => $get('limit_history_count')),
                    ]),
                Toggle::make('sendmail_success')
                    ->label(__('filament-database-schedule::schedule.fields.sendmail_success')),
                Toggle::make('sendmail_error')
                    ->label(__('filament-database-schedule::schedule.fields.sendmail_error')),
                Toggle::make('even_in_maintenance_mode')
                    ->label(__('filament-database-schedule::schedule.fields.even_in_maintenance_mode')),
                Toggle::make('without_overlapping')
                    ->label(__('filament-database-schedule::schedule.fields.without_overlapping')),
                Toggle::make('on_one_server')
                    ->label(__('filament-database-schedule::schedule.fields.on_one_server')),
                Toggle::make('run_in_background')
                    ->label(__('filament-database-schedule::schedule.fields.run_in_background')),
            ])->columns(1);
    }
}
