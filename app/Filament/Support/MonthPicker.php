<?php

namespace App\Filament\Support;

use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Carbon;

/**
 * A date picker shown as month/year (e.g. 06/2021).
 * Whatever day is picked, the first of that month is saved.
 */
final class MonthPicker
{
    public static function make(string $name, string $label): DatePicker
    {
        return DatePicker::make($name)
            ->label($label)
            ->native(false)
            ->displayFormat('m/Y')
            ->closeOnDateSelection()
            ->dehydrateStateUsing(fn (?string $state) => $state
                ? Carbon::parse($state)->startOfMonth()->toDateString()
                : null);
    }
}
