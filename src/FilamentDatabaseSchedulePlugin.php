<?php

namespace HusamTariq\FilamentDatabaseSchedule;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentDatabaseSchedulePlugin implements Plugin
{

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }


    public function getId(): string
    {
        return 'filament-database-schedule';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources(config('filament-database-schedule.resources'))
        ;
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
