<?php

namespace HusamTariq\FilamentDatabaseSchedule\Observer;

use HusamTariq\FilamentDatabaseSchedule\Enums\Status;
use HusamTariq\FilamentDatabaseSchedule\Http\Services\ScheduleHistoryService;
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
        if ($schedule->limit_history_count === true && $schedule->isDirty(['limit_history_count','max_history_count'])) {
            ScheduleHistoryService::prune($schedule);
        }
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
