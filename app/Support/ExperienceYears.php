<?php

namespace App\Support;

use App\Models\Experience;
use Illuminate\Support\Carbon;

/**
 * Years of experience, counted from the positions entered in the admin panel.
 *
 * Each position counts from its start month to the end of its end month
 * (or today when it is ongoing or ends in the future). Overlapping positions are merged so the
 * same months are never counted twice, and gaps between jobs are left out.
 */
final class ExperienceYears
{
    public static function total(?Carbon $today = null): int
    {
        $today ??= Carbon::now();

        $periods = Experience::query()
            ->whereNotNull('start_date')
            ->get(['start_date', 'end_date'])
            ->map(fn (Experience $x) => [
                $x->start_date->copy()->startOfMonth(),
                ($x->end_date ?? $today)->copy()->min($today)->startOfMonth()->addMonth(),
            ])
            ->filter(fn (array $p) => $p[0]->lt($p[1]))
            ->sortBy(fn (array $p) => $p[0]->timestamp)
            ->values();

        if ($periods->isEmpty()) {
            return (int) config('portfolio.stats.years');
        }

        $months = 0;
        [$start, $end] = $periods->first();

        foreach ($periods->skip(1) as [$nextStart, $nextEnd]) {
            if ($nextStart->lte($end)) {
                $end = $end->max($nextEnd);

                continue;
            }

            $months += (int) $start->diffInMonths($end);
            [$start, $end] = [$nextStart, $nextEnd];
        }

        $months += (int) $start->diffInMonths($end);

        return intdiv($months, 12);
    }
}
