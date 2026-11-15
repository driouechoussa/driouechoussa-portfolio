@extends('layouts.app')

@php
    $replace = ['name' => $profile['name'], 'email' => $profile['email']];
    // Update this date whenever the policy text changes.
    $updated = Illuminate\Support\Carbon::create(2026, 11, 15)->locale(app()->getLocale())->isoFormat('LL');
@endphp

@section('page', 'privacy')
@section('title', __('ui.meta.privacy'))
@section('description', __('ui.metaDesc.privacy'))

@section('content')

  <section class="page-hero" aria-labelledby="pageTitle">
    <div class="container">
      <nav class="breadcrumb-ui" aria-label="{{ __('ui.common.breadcrumb') }}">
        <a href="{{ localized_route('home') }}">{{ __('ui.nav.home') }}</a>
        <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
        <span aria-current="page">{{ __('ui.footer.privacy') }}</span>
      </nav>
      <div class="hero-enter">
        <span class="eyebrow">{{ __('privacy.eyebrow') }}</span>
        <h1 id="pageTitle">{!! __('privacy.title') !!}</h1>
        <p class="lead-text">{{ __('privacy.lead') }}</p>
      </div>
    </div>
  </section>

  <section class="section pt-4">
    <div class="container">
      <div class="prose-ui">
        <p class="updated">{{ __('privacy.updated', ['date' => $updated]) }}</p>

        @foreach (__('privacy.sections') as $section)
          <h2>{{ $section['heading'] }}</h2>
          @foreach ($section['body'] as $paragraph)
            <p>{{ strtr($paragraph, [':name' => $replace['name'], ':email' => $replace['email']]) }}</p>
          @endforeach
          @isset($section['list'])
            <ul>
              @foreach ($section['list'] as $item)
                <li>{{ $item }}</li>
              @endforeach
            </ul>
          @endisset
          @isset($section['after'])
            <p>{{ $section['after'] }}</p>
          @endisset
        @endforeach
      </div>
    </div>
  </section>
@endsection
