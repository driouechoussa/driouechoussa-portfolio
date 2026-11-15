<?php

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    use HasSortOrder;

    protected $fillable = ['name', 'icon', 'sort_order'];
}
