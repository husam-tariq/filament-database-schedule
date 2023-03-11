<?php

namespace HusamTariq\FilamentDatabaseSchedule\Filament\Columns;

use Filament\Tables\Columns\TagsColumn;

class ScheduleArguments extends TagsColumn
{
 //   protected string $view = 'filament-database-schedule::columns.schedule-arguments';

  protected bool $withValue = true;

  public function withValue(bool $withValue = true): static
  {
      $this->withValue = $withValue;

      return $this;
  }

  public function getTags(): array
  {
      $tags = $this->getState();

      if (is_array($tags)) {
        if($this->withValue)
          return collect($tags)->filter(fn($value)=>!empty($value['value']))->map(fn ($value, $key)=> ($value['name']??$key).'='.$value['value'])->toArray();
          else
          return collect($tags)->map(fn ($value, $key)=> $key.'='.$value)->toArray();

      }

      if (! ($separator = $this->getSeparator())) {
          return [];
      }

      $tags = explode($separator, $tags);

      if (count($tags) === 1 && blank($tags[0])) {
          $tags = [];
      }

      return $tags;
  }
}
