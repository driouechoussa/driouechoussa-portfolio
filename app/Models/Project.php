<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedFields;
use App\Models\Concerns\HasSortOrder;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasLocalizedFields, HasSortOrder;

    protected $fillable = [
        'slug', 'category', 'title', 'preview', 'icon', 'accent', 'image', 'year', 'client',
        'live_url', 'source_url',
        'duration', 'role', 'stack', 'description', 'overview', 'challenge',
        'solution', 'results', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'duration' => 'array',
            'role' => 'array',
            'stack' => 'array',
            'description' => 'array',
            'overview' => 'array',
            'challenge' => 'array',
            'solution' => 'array',
            'results' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
