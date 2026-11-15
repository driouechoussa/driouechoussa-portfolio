@extends('layouts.app')

@php
    $description = $project->localized('description');
    $image = App\Support\Media::url($project->image);
@endphp

@section('page', 'project-details')
@section('title', $project->title.' — '.$profile['name'])
@section('description', $description)
@if ($image)
  @section('og_image', $image)
@endif

@push('structured-data')
<script type="application/ld+json" nonce="{{ Vite::cspNonce() }}">
{!! json_encode(array_filter([
    '@context' => 'https://schema.org',
    '@type' => 'CreativeWork',
    'name' => $project->title,
    'description' => $description,
    'url' => localized_route('projects.show', $project),
    'image' => $image,
    'dateCreated' => $project->year,
    'inLanguage' => app()->getLocale(),
    'keywords' => implode(', ', $project->stack),
    'creator' => ['@type' => 'Person', 'name' => $profile['name'], 'url' => localized_route('home')],
]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
</script>
@endpush

@section('content')

  <div data-render="project-detail" data-id="{{ $project->slug }}">
    {{-- Server-rendered summary for search engines and link previews; replaced by the scripts. --}}
    <article class="ssr-fallback container py-5">
      <h1>{{ $project->title }}</h1>
      <p>{{ $description }}</p>
      <h2>{{ __('ui.pd.overview') }}</h2>
      <p>{{ $project->localized('overview') }}</p>
      <h2>{{ __('ui.pd.challenge') }}</h2>
      <p>{{ $project->localized('challenge') }}</p>
      <h2>{{ __('ui.pd.solution') }}</h2>
      <p>{{ $project->localized('solution') }}</p>
      <h2>{{ __('ui.pd.results') }}</h2>
      <ul>
        @foreach ($project->results as $result)
          <li>{{ $result['value'] }} — {{ App\Models\Project::pickLocale($result['label']) }}</li>
        @endforeach
      </ul>
      <p>{{ __('ui.pd.stack') }}: {{ implode(', ', $project->stack) }}</p>
    </article>
  </div>

  @include('sections.cta')
@endsection
