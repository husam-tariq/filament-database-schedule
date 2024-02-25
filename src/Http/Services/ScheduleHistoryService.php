<?php


namespace HusamTariq\FilamentDatabaseSchedule\Http\Services;

class ScheduleHistoryService
{
    public static function prune($schedule) {
        $keepIds = $schedule->histories()->select('id')->latest()->take($schedule->max_history_count);
        $schedule->histories()->whereNotIn('id', $keepIds)->delete();
    }
}
