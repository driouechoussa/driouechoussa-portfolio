<?php

namespace App\Models\Concerns;

use Illuminate\Support\Carbon;

/**
 * A start/end period stored as dates but shown as month/year.
 * An empty end date means the period is still ongoing.
 *
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 */
trait HasMonthPeriod
{
    public const MONTH_FORMAT = 'm/Y';

    public function initializeHasMonthPeriod(): void
    {
        $this->mergeCasts([
            'start_date' => 'date',
            'end_date' => 'date',
        ]);
    }

    public function startMonth(): ?string
    {
        return $this->start_date?->format(self::MONTH_FORMAT);
    }

    public function endMonth(): ?string
    {
        return $this->end_date?->format(self::MONTH_FORMAT);
    }
}
