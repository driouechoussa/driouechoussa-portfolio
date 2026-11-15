<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    public const SUBJECTS = ['project', 'freelance', 'job', 'other'];

    public const BUDGETS = ['lt1k', '1-5k', '5-10k', 'gt10k'];

    protected $fillable = [
        'name', 'email', 'subject', 'budget', 'message', 'locale', 'ip_address', 'user_agent', 'read_at',
    ];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }
}
