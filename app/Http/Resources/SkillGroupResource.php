<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\SkillGroup */
class SkillGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'group' => $this->key,
            'icon' => $this->icon,
            // The front end expects [name, level] pairs.
            'items' => $this->skills->map(fn ($skill) => [$skill->name, $skill->level])->all(),
        ];
    }
}
