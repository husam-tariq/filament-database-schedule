<?php

namespace HusamTariq\FilamentDatabaseSchedule\Filament\Resources\Schedules;

use HusamTariq\FilamentDatabaseSchedule\Filament\Resources\Schedules\Pages\CreateSchedule;
use HusamTariq\FilamentDatabaseSchedule\Filament\Resources\Schedules\Pages\EditSchedule;
use HusamTariq\FilamentDatabaseSchedule\Filament\Resources\Schedules\Pages\ListSchedules;
use HusamTariq\FilamentDatabaseSchedule\Filament\Resources\Schedules\Pages\ViewSchedule;
use HusamTariq\FilamentDatabaseSchedule\Filament\Resources\Schedules\Schemas\ScheduleForm;
use HusamTariq\FilamentDatabaseSchedule\Filament\Resources\Schedules\Tables\SchedulesTable;
use BackedEnum;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ScheduleResource extends Resource
{
    public static function getModel(): string
    {
        return config('filament-database-schedule.model');
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return config('filament-database-schedule.navigation_icon');
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return config('filament-database-schedule.route_slug');
    }

    public static function getModelLabel(): string
    {
        return __('filament-database-schedule::schedule.resource.single');
    }


    public static function getPluralModelLabel(): string
    {
        return __('filament-database-schedule::schedule.resource.plural');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('filament-database-schedule::schedule.resource.navigation');
    }



    public static function form(Schema $schema): Schema
    {
        return ScheduleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SchedulesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSchedules::route('/'),
            'create' => CreateSchedule::route('/create'),
            'view' => ViewSchedule::route('/{record}'),
            'edit' => EditSchedule::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
