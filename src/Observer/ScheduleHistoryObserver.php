<?php

namespace HusamTariq\FilamentDatabaseSchedule\Observer;

use HusamTariq\FilamentDatabaseSchedule\Http\Services\ScheduleHistoryService;
use HusamTariq\FilamentDatabaseSchedule\Models\ScheduleHistory;

class ScheduleHistoryObserver
{
    public function created(ScheduleHistory $scheduleHistory): void
    {
        $schedule = $scheduleHistory->command()->first();
        if($schedule->limit_history_count) {
            ScheduleHistoryService::prune($schedule);
        }
    }
}
