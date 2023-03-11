<?php


namespace HusamTariq\FilamentDatabaseSchedule\Http\Services;

class ScheduleService
{
    private $model;

    public function __construct()
    {
        $this->model = app(config('filament-database-schedule.model'));
    }

    public function getActives()
    {
        if (config('filament-database-schedule.cache.enabled')) {
            return $this->getFromCache();
        }
        return $this->model->active()->get();
    }

    public function clearCache()
    {
        $store = config('filament-database-schedule.cache.store');
        $key = config('filament-database-schedule.cache.key');

        cache()->store($store)->forget($key);
    }

    private function getFromCache()
    {
        $store = config('filament-database-schedule.cache.store');
        $key = config('filament-database-schedule.cache.key');

        return cache()->store($store)->rememberForever($key, function () {
            return $this->model->active()->get();
        });
    }
}
