<?php

namespace App\Models;

use App\Models\Concerns\HasMonthPeriod;
use App\Models\Concerns\HasSortOrder;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasMonthPeriod, HasSortOrder;

    protected $fillable = [
        'role', 'company', 'location', 'description', 'start_date', 'end_date', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'role' => 'array',
            'location' => 'array',
            'description' => 'array',
        ];
    }
}
