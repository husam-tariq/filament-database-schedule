<?php

namespace HusamTariq\FilamentDatabaseSchedule;

use BladeUI\Icons\Factory;
use Filament\PluginServiceProvider;
use HusamTariq\FilamentDatabaseSchedule\Console\Commands\PhpUnitTestJobCommand;
use HusamTariq\FilamentDatabaseSchedule\Console\Commands\ScheduleClearCacheCommand;
use HusamTariq\FilamentDatabaseSchedule\Console\Commands\TestJobCommand;
use HusamTariq\FilamentDatabaseSchedule\Console\Scheduling\Schedule;
use HusamTariq\FilamentDatabaseSchedule\Filament\Resources\ScheduleResource;
use HusamTariq\FilamentDatabaseSchedule\Observer\ScheduleObserver;
use Spatie\LaravelPackageTools\Package;
use Illuminate\Console\Scheduling\Schedule as BaseSchedule;

class FilamentDatabaseScheduleServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-database-schedule';

    protected array $resources = [
        ScheduleResource::class,
    ];

    protected array $pages = [
        // CustomPage::class,
    ];

    protected array $widgets = [
        // CustomWidget::class,
    ];

    protected array $styles = [
        'plugin-filament-database-schedule' => __DIR__ . '/../resources/dist/filament-database-schedule.css',
    ];

    protected array $scripts = [
        'plugin-filament-database-schedule' => __DIR__ . '/../resources/dist/filament-database-schedule.js',
    ];

    // protected array $beforeCoreScripts = [
    //     'plugin-filament-database-schedule' => __DIR__ . '/../resources/dist/filament-database-schedule.js',
    // ];




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

        $this->app->resolving(BaseSchedule::class, function ($schedule) {
            $schedule = app(Schedule::class, ['schedule' => $schedule]);
            return $schedule->execute();
        });

        $this->commands([
            TestJobCommand::class,
            PhpUnitTestJobCommand::class,
            ScheduleClearCacheCommand::class,
        ]);
        parent::boot();
    }
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
}
