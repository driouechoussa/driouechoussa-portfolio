<?php

namespace App\Models;

use App\Models\Concerns\HasMonthPeriod;
use App\Models\Concerns\HasSortOrder;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasMonthPeriod, HasSortOrder;

    protected $table = 'education';

    protected $fillable = [
        'school', 'degree', 'field', 'description', 'start_date', 'end_date', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'school' => 'array',
            'degree' => 'array',
            'field' => 'array',
            'description' => 'array',
        ];
    }
}
