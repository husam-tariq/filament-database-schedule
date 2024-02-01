<?php

namespace HusamTariq\FilamentDatabaseSchedule\Console\Scheduling;

use HusamTariq\FilamentDatabaseSchedule\Http\Services\ScheduleService;
use HusamTariq\FilamentDatabaseSchedule\Models\ScheduleHistory;
use \Illuminate\Console\Scheduling\Schedule as BaseSchedule;
use Illuminate\Support\Facades\Log;

class Schedule
{
    /**
     * @var BaseSchedule
     */
    private $schedule;

    private $tasks;

    private $history;

    public function __construct(ScheduleService $scheduleService, BaseSchedule $schedule)
    {
        $this->tasks = $scheduleService->getActives();
        $this->schedule = $schedule;
    }

    public function execute()
    {
        foreach ($this->tasks as $task) {
            $this->dispatch($task);
        }
    }

    /**
     * @throws \Exception
     */
    private function dispatch($task)
    {
        $model = config('filament-database-schedule.model');
        if ($task instanceof $model) {
            // @var Event $event
            if ($task->command === 'custom') {
                $command = $task->command_custom;
                $event = $this->schedule->exec($command);
            } else {
                $command = $task->command;
                $event = $this->schedule->command(
                    $command,
                    array_values($task->getArguments()) + $task->getOptions()
                );
            }
            $event->cron($task->expression);

            //ensure output is being captured to write history
            $event->storeOutput();

            if (!empty($task->environments)) {
                $event->environments($task->environments);
            }

            if ($task->even_in_maintenance_mode) {
                $event->evenInMaintenanceMode();
            }

            if ($task->without_overlapping) {
                $event->withoutOverlapping();
            }

            if ($task->run_in_background) {
                $event->runInBackground();
            }

            if (!empty($task->webhook_before)) {
                $event->pingBefore($task->webhook_before);
            }

            if (!empty($task->webhook_after)) {
                $event->thenPing($task->webhook_after);
            }

            if (!empty($task->email_output)) {
                if ($task->sendmail_success) {
                    $event->emailOutputTo($task->email_output);
                }

                if ($task->sendmail_error) {
                    $event->emailOutputOnFailure($task->email_output);
                }
            }

            if (!empty($task->on_one_server)) {
                $event->onOneServer();
            }

            $event->before(function () use ($task, $command){
                $this->history = $this->createHistoryEntry($task, $command);
            });

            $event->onSuccess(
                function () use ($task, $event) {
                    $this->createLogFile($task, $event);
                    if ($task->log_success) {
                        $this->updateHistoryEntry($this->history, $event);
                    }
                }
            );

            $event->onFailure(
                function () use ($task, $event) {
                    $this->createLogFile($task, $event, 'critical');
                    if ($task->log_error) {
                        $this->updateHistoryEntry($this->history, $event);
                    }
                }
            );

            $event->after(function () use ($event) {
                unlink($event->output);
            });

            unset($event);
        } else {
            throw new \Exception('Task with invalid instance type');
        }
    }

    private function createLogFile($task, $event, $type = 'info')
    {
        if ($task->log_filename) {
            $logChannel = Log::build([
                'driver' => 'single',
                'path' => storage_path('logs/' . $task->log_filename . '.log'),
            ]);
            Log::stack([$logChannel])->$type(file_get_contents($event->output));
        }
    }

    private function createHistoryEntry($task, $command) : ScheduleHistory
    {
        $task->histories()->create(
            [
                'command' => $command,
                'params' => $task->getArguments(),
                'options' => $task->getOptions(),
                'output' => "Processing ..."
            ]
        );
        $history = $task->histories()->latest()->first();
        return $history;
    }

    private function updateHistoryEntry(ScheduleHistory $history, $event)
    {
        $history->update(
            [
                'output' => file_get_contents($event->output)
            ]
        );
    }
}
