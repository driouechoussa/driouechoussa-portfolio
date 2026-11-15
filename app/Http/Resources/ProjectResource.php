<?php

namespace App\Http\Resources;

use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Project */
class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->slug,
            'category' => $this->category,
            'title' => $this->title,
            'preview' => $this->preview,
            'icon' => $this->icon,
            'accent' => $this->accent,
            'image' => Media::url($this->image),
            'year' => $this->year,
            'client' => $this->client,
            'liveUrl' => $this->live_url,
            'sourceUrl' => $this->source_url,
            'duration' => $this->duration,
            'role' => $this->role,
            'stack' => $this->stack,
            'desc' => $this->description,
            'overview' => $this->overview,
            'challenge' => $this->challenge,
            'solution' => $this->solution,
            'results' => $this->results,
        ];
    }
}
