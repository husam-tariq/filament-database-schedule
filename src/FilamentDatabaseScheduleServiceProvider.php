<?php

namespace HusamTariq\FilamentDatabaseSchedule;

use BladeUI\Icons\Factory;
use HusamTariq\FilamentDatabaseSchedule\Console\Commands\PhpUnitTestJobCommand;
use HusamTariq\FilamentDatabaseSchedule\Console\Commands\ScheduleClearCacheCommand;
use HusamTariq\FilamentDatabaseSchedule\Console\Commands\TestJobCommand;
use HusamTariq\FilamentDatabaseSchedule\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Schema;
use Filament\Support\Facades\FilamentAsset;
use HusamTariq\FilamentDatabaseSchedule\Models\ScheduleHistory;
use HusamTariq\FilamentDatabaseSchedule\Observer\ScheduleHistoryObserver;
use HusamTariq\FilamentDatabaseSchedule\Observer\ScheduleObserver;
use Spatie\LaravelPackageTools\Package;
use Illuminate\Console\Scheduling\Schedule as BaseSchedule;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentDatabaseScheduleServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-database-schedule';
    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasMigrations()
            ->hasViews()
            ->hasCommands()
            ->hasTranslations();
    }

    public function packageBooted(): void
    {
        FilamentAsset::register($this->getAssets(), package: $this->getAssetPackageName());
    }

    protected function getAssetPackageName(): ?string
    {
        return static::$name;
    }

    protected function getAssets(): array
    {
        return [

        ];

    }

    public function register()
    {
        $this->callAfterResolving(Factory::class, function (Factory $factory) {
            $factory->add('filament-schedule-icons', [
                'path' => __DIR__ . '/../resources/svg',
                'prefix' => 'schedule',
            ]);
        });
        parent::register();
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../resources/svg' => public_path('vendor/' . static::$name),
            ], static::$name);
        }
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], 'filament-database-schedule-migrations');

        $config = $this->app['config'];
        $model = $config->get('filament-database-schedule.model');
        $model::observe(ScheduleObserver::class);
        ScheduleHistory::observe(ScheduleHistoryObserver::class);

        try {
            if (Schema::hasTable($config->get('filament-database-schedule.table.schedules', 'schedules'))) {
                $this->app->resolving(BaseSchedule::class, function ($schedule) {
                    $schedule = app(Schedule::class, ['schedule' => $schedule]);
                    return $schedule->execute();
                });
            }
        } catch (\Exception $e) {
        }

        $this->commands([
            TestJobCommand::class,
            PhpUnitTestJobCommand::class,
            ScheduleClearCacheCommand::class,
        ]);
        parent::boot();
    }


}
