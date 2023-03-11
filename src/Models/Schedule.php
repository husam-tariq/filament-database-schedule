<?php

namespace HusamTariq\FilamentDatabaseSchedule\Models;

use Illuminate\Console\Scheduling\ManagesFrequencies;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;

class Schedule extends Model
{
    use ManagesFrequencies;
    use SoftDeletes;

    public const STATUS_INACTIVE = 0;
    public const STATUS_ACTIVE = 1;
    public const STATUS_TRASHED = 2;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table;

    protected $fillable = [
        'command',
        'command_custom',
        'params',
        'options',
        'options_with_value',
        'expression',
        'even_in_maintenance_mode',
        'without_overlapping',
        'on_one_server',
        'webhook_before',
        'webhook_after',
        'email_output',
        'sendmail_error',
        'sendmail_success',
        'log_success',
        'log_error',
        'status',
        'run_in_background',
        'log_filename',
        'environments',
    ];

    protected $attributes = [
        'expression' => '* * * * *',
        'params' => '{}',
        'options' => '{}',
        'options_with_value' => '{}',
    ];

    protected $casts = [
        'params' => 'array',
        'options' => 'array',
        'options_with_value' => 'array',
        'environments' => 'array',
    ];

    /**
     * Creates a new instance of the model.
     *
     * @param array $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = Config::get('filament-database-schedule.table.schedules', 'schedules');
    }

    public function histories()
    {
        return $this->hasMany(ScheduleHistory::class, 'schedule_id', 'id');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function getArguments(): array
    {
        $arguments = [];

        foreach (($this->params ?? []) as $argument => $value) {
            if (empty($value['value'])) {
                continue;
            }
            if (isset($value["type"]) && $value['type'] === 'function') {
                eval('$arguments[$argument] = (string) ' . $value['value']);
            } else {
                $arguments[$value['name']??$argument] = $value['value'];
            }
        }

        return $arguments;
    }

    public function getOptions(): array
    {
        $options = $this->options ?? [];
        $options_with_value = $this->options_with_value[0] ?? [];
        if (!empty($options_with_value))
            array_push($options, $options_with_value);

        return collect($options)->map(function ($value, $key) {
            if (!is_array($value) || isset($value['value'])) {
                if (is_array($value)) {
                    return "--" . ($value['name']??$key) . "=" . $value['value'];
                } else {
                    return "--$value";
                }
            }
        })->toArray();
    }

    public static function getEnvironments()
    {
        return static::whereNotNull('environments')
            ->groupBy('environments')
            ->get('environments')
            ->pluck('environments', 'environments');
    }
}
