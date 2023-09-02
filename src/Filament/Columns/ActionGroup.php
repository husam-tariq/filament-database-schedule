<?php

namespace HusamTariq\FilamentDatabaseSchedule\Filament\Columns;

use Filament\Actions\Concerns\InteractsWithRecord;
use Filament\Tables\Actions\ActionGroup as ActionsActionGroup;

class ActionGroup extends ActionsActionGroup
{
    use InteractsWithRecord;

    protected string $view = 'filament-database-schedule::components.action-group';
    public const ICON_BUTTON_VIEW = 'filament-database-schedule::components.action-group';
    public function getActions(): array
    {
        $actions = [];

        foreach ($this->actions as $action) {
            $actions[$action->getName()] = $action
            ->view('filament-database-schedule::actions.button-action')->size('md')
            ->record($this->getRecord());
        }

        return $actions;
    }
}
