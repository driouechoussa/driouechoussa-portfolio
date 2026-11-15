<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Experience */
class ExperienceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'start' => $this->start_year,
            'end' => $this->end_year,
            'role' => $this->role,
            'company' => $this->company,
            'location' => $this->location,
            'desc' => $this->description,
        ];
    }
}
