<?php

namespace HusamTariq\FilamentDatabaseSchedule\Rules;

use Cron\CronExpression;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Corn implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!CronExpression::isValidExpression($value)) {
            $fail(trans('filament-database-schedule::schedule.validation.cron'));
        }
    }
}
