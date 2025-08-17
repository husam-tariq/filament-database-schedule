<?php

namespace HusamTariq\FilamentDatabaseSchedule\Http\Services;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Collection;

class CommandService
{
    public static function get(): Collection
    {
        $commands = collect(app(Kernel::class)->all())->sortKeys();
        $commandsKeys = $commands->keys()->toArray();
        if (config('filament-database-schedule.commands.show_supported_only')) {
            foreach (config('filament-database-schedule.commands.supported') as $supported) {
                $commandsKeys = preg_grep("/^$supported/", $commandsKeys);
            }
        } else {
            foreach (config('filament-database-schedule.commands.exclude') as $exclude) {
                $commandsKeys = preg_grep("/^$exclude/", $commandsKeys, PREG_GREP_INVERT);
            }
        }

        return $commands->only($commandsKeys)
            ->map(function ($command) {
                return [
                    'name' => $command->getName(),
                    'description' => $command->getDescription(),
                    'signature' => $command->getSynopsis(),
                    'full_name' => $command->getName() . ' (' . $command->getDescription() . ")",
                    'arguments' => static::getArguments($command),
                    'options' => static::getOptions($command),
                ];
            });
    }

    private static function getArguments($command): array
    {
        $arguments = [];
        foreach ($command->getDefinition()->getArguments() as $argument) {
            $arguments[] = [
                'name' => $argument->getName(),
                'default' => $argument->getDefault(),
                'required' => $argument->isRequired()
            ];
        }

        return $arguments;
    }

    private static function getOptions($command): array
    {
        $options = [
            'withValue' => [],
            'withoutValue' => [
                'verbose',
                'quiet',
                'ansi',
                'no-ansi',
            ]
        ];
        foreach ($command->getDefinition()->getOptions() as $option) {
            if ($option->acceptValue()) {
                $options['withValue'][] = (object) [
                    'name' => $option->getName(),
                    'default' => $option->getDefault(),
                    'required' => $option->isValueRequired()
                ];
            } else {
                $options['withoutValue'][] = $option->getName();
            }
        }

        return $options;
    }
}
