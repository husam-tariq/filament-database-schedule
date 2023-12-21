<?php

namespace HusamTariq\FilamentDatabaseSchedule\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Status: string implements HasIcon, HasColor, HasLabel {

    case Active = 'active';
    case Inactive = 'inactive';
    case Trashed = 'trashed';

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive => 'warning',
            self::Trashed => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Active => 'heroicon-o-check-circle',
            self::Inactive => 'heroicon-o-document',
            self::Trashed => 'heroicon-o-x-circle',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Active => __('filament-database-schedule::schedule.status.active'),
            self::Inactive => __('filament-database-schedule::schedule.status.inactive'),
            self::Trashed => __('filament-database-schedule::schedule.status.trashed'),
        };
    }
}