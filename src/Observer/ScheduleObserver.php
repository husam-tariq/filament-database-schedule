<?php

namespace HusamTariq\FilamentDatabaseSchedule\Observer;

use HusamTariq\FilamentDatabaseSchedule\Enums\Status;
use HusamTariq\FilamentDatabaseSchedule\Http\Services\ScheduleService;
use HusamTariq\FilamentDatabaseSchedule\Models\Schedule;

class ScheduleObserver
{
    public function created()
    {
        $this->clearCache();
    }

    public function updated(Schedule $schedule)
    {
        $this->clearCache();
    }

    public function deleted(Schedule $schedule)
    {

        $schedule->status = Status::Trashed;
        $schedule->saveQuietly();
        $this->clearCache();
    }

    public function restored(Schedule $schedule)
    {
        $schedule->status = Status::Inactive;
        $schedule->saveQuietly();
    }

    public function saved(Schedule $schedule)
    {
        $this->clearCache();
    }

    protected function clearCache()
    {
        if (config('filament-database-schedule.cache.enabled')) {
            $scheduleService = app(ScheduleService::class);
            $scheduleService->clearCache();
        }
    }
}
