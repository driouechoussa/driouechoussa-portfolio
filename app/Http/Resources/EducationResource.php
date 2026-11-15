<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Education */
class EducationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'start' => $this->start_year,
            'end' => $this->end_year,
            'school' => $this->school,
            'degree' => $this->degree,
            'field' => $this->field,
            'desc' => $this->description,
        ];
    }
}
