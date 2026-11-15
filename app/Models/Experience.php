<?php

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasSortOrder;

    protected $fillable = [
        'role', 'company', 'location', 'description', 'start_year', 'end_year', 'sort_order',
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
