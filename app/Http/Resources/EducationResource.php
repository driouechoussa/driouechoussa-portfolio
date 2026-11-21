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
            'start' => $this->startMonth(),
            'end' => $this->endMonth(),
            'school' => $this->school,
            'degree' => $this->degree,
            'field' => $this->field,
            'desc' => $this->description,
        ];
    }
}
