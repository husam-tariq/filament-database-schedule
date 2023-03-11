<?php

namespace HusamTariq\FilamentDatabaseSchedule\Filament\Resources\ScheduleResource\Pages;

use Awcodes\FilamentTableRepeater\Components\TableRepeater;
use Closure;
use HusamTariq\FilamentDatabaseSchedule\Filament\Resources\ScheduleResource;
use Filament\Forms;
use Filament\Resources\Pages\CreateRecord;
use HusamTariq\FilamentDatabaseSchedule\Http\Services\CommandService;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class CreateSchedule extends CreateRecord
{

    protected static string $resource = ScheduleResource::class;
    public Collection $commands;

    public function mount(): void
    {
        $this->commands = CommandService::get();
        parent::mount();
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->context('create')
                ->model($this->getModel())
                ->schema($this->getFormSchema())
                // ->columns(2)
                ->statePath('data')
                ->inlineLabel(false),
        ];
    }
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('command')->label(__('filament-database-schedule::schedule.fields.command'))
                ->options($this->commands->pluck('full_name', 'name')->prepend(__('filament-database-schedule::schedule.messages.custom'), 'custom'))
                ->reactive()
                ->searchable()
                ->required()
                ->afterStateUpdated(function (Closure $set, $state) {
                    $set('params', $this->commands->firstWhere('name', $state)['arguments'] ?? []);
                    $set('options_with_value', $this->commands->firstWhere('name', $state)['options']["withValue"] ?? []);
                }),
            Forms\Components\TextInput::make('command_custom')
                ->placeholder(__('filament-database-schedule::schedule.messages.custom-command-here'))
                ->label(__('filament-database-schedule::schedule.messages.custom'))
                ->required()
                ->visible(fn (Closure $get) => $get('command') === 'custom'),

            TableRepeater::make('params')->label(__('filament-database-schedule::schedule.fields.arguments'))
                ->schema([
                    Forms\Components\TextInput::make('value')->label(fn (Closure $get) => ucfirst($get('name')))->required(fn (Closure $get) => $get('required')),
                    Forms\Components\Hidden::make('type')->default('string'),
                    Forms\Components\Hidden::make('name'),
                ])->disableItemCreation()->withoutHeader()->disableItemDeletion()->disableItemMovement()
                ->columnSpan('full')->visible(fn (Closure $get) => !empty($this->commands->firstWhere('name', $get('command'))['arguments'])),
            TableRepeater::make('options_with_value')->label(__('filament-database-schedule::schedule.fields.options_with_value'))
                ->schema([
                    Forms\Components\TextInput::make('value')->label(fn (Closure $get) => ucfirst($get('name')))->required(fn (Closure $get) => $get('required')),
                    Forms\Components\Hidden::make('type')->default('string'),
                    Forms\Components\Hidden::make('name'),
                ])->disableItemCreation()->withoutHeader()->disableItemDeletion()->disableItemMovement()->default([])
                ->columnSpan('full')->visible(fn ($state) => !empty($state)),
            Forms\Components\CheckboxList::make('options')->label(__('filament-database-schedule::schedule.fields.options'))
                ->options(
                    fn (Closure $get) =>
                    collect($this->commands->firstWhere('name', $get('command'))['options']['withoutValue'] ?? [])
                        ->mapWithKeys(function ($value) {
                            return [$value => $value];
                        }),
                )->columns(3)->columnSpanFull()->visible(fn (Forms\Components\CheckboxList $component) => !empty($component->getOptions())),
            Forms\Components\TextInput::make('expression')
                ->placeholder('* * * * *')
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

        ];
    }


}
