@extends('layouts.app')

@section('page', 'about')
@section('title', __('ui.meta.about'))
@section('description', __('ui.metaDesc.about'))

@include('partials.schema.person')

@section('content')

  @include('sections.page-hero', ['page' => 'about', 'i18n' => 'about'])

  <!-- ===== STORY ===== -->
  <section class="section pt-4" aria-labelledby="storyTitle">
    <div class="container">
      <div class="row g-5 align-items-center">
        <div class="col-lg-5 reveal" data-reveal="left">
          <div class="about-photo">
            <picture>
              <source srcset="{{ asset('images/about-portrait.webp') }}" type="image/webp">
              <img src="{{ asset('images/about-portrait.jpg') }}" data-i18n-alt="hero.photoAlt" alt="{{ __('ui.hero.photoAlt') }}" width="900" height="1310" loading="lazy" draggable="false">
            </picture>
          </div>
        </div>
        <div class="col-lg-7 reveal" data-reveal="right">
          <span class="eyebrow" data-i18n="about.storyEyebrow">{{ __('ui.about.storyEyebrow') }}</span>
          <h2 class="section-title" id="storyTitle" data-i18n-html="about.storyTitle">{!! __('ui.about.storyTitle') !!}</h2>
          <p data-i18n="about.p1">{{ __('ui.about.p1') }}</p>
          <p data-i18n="about.p2">{{ __('ui.about.p2') }}</p>
          <p data-i18n="about.p3">{{ __('ui.about.p3') }}</p>
          <ul class="about-info">
            <li><span data-i18n="about.info.name">{{ __('ui.about.info.name') }}</span><strong data-profile="name">{{ $profile['name'] }}</strong></li>
            <li><span data-i18n="about.info.location">{{ __('ui.about.info.location') }}</span><strong data-profile="location">{{ $profile['location'] }}</strong></li>
            <li><span data-i18n="about.info.email">{{ __('ui.about.info.email') }}</span><a data-profile-href="email" data-profile="email" href="{{ $profile['href']['email'] }}">{{ $profile['email'] }}</a></li>
            <li><span data-i18n="about.info.freelance">{{ __('ui.about.info.freelance') }}</span><strong class="text-primary-ui" data-i18n="about.info.available">{{ __('ui.about.info.available') }}</strong></li>
          </ul>
          <div class="id-actions">
            <a class="btn-ui btn-ui--primary btn-ui--lg" data-profile-href="cv" href="{{ $profile['href']['cv'] }}" download><i class="fa-solid fa-download" aria-hidden="true"></i><span data-i18n="hero.cv">{{ __('ui.hero.cv') }}</span></a>
            <a class="btn-ui btn-ui--outline btn-ui--lg" href="{{ localized_route('contact') }}"><span data-i18n="nav.contact">{{ __('ui.nav.contact') }}</span><i class="fa-solid fa-arrow-right dir-icon" aria-hidden="true"></i></a>
          </div>
        </div>
      </div>
      <div class="stats">
        <div class="stat reveal">
          <p class="stat-value" data-count="{{ config('portfolio.stats.years') }}"><span class="count-num">{{ config('portfolio.stats.years') }}</span><span class="plus">+</span></p>
          <p class="stat-label" data-i18n="stats.years">{{ __('ui.stats.years') }}</p>
        </div>
        <div class="stat reveal">
          <p class="stat-value" data-count="{{ config('portfolio.stats.projects') }}"><span class="count-num">{{ config('portfolio.stats.projects') }}</span><span class="plus">+</span></p>
          <p class="stat-label" data-i18n="stats.projects">{{ __('ui.stats.projects') }}</p>
        </div>
        <div class="stat reveal">
          <p class="stat-value" data-count="{{ config('portfolio.stats.clients') }}"><span class="count-num">{{ config('portfolio.stats.clients') }}</span><span class="plus">+</span></p>
          <p class="stat-label" data-i18n="stats.clients">{{ __('ui.stats.clients') }}</p>
        </div>
        <div class="stat reveal">
          <p class="stat-value" data-count="{{ config('portfolio.stats.tech') }}"><span class="count-num">{{ config('portfolio.stats.tech') }}</span><span class="plus">+</span></p>
          <p class="stat-label" data-i18n="stats.tech">{{ __('ui.stats.tech') }}</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== SKILLS ===== -->
  <section class="section section--alt" aria-labelledby="skillsTitle">
    <div class="container">
      <div class="section-head section-head--center reveal">
        <span class="eyebrow" data-i18n="about.skillsEyebrow">{{ __('ui.about.skillsEyebrow') }}</span>
        <h2 class="section-title" id="skillsTitle" data-i18n-html="about.skillsTitle">{!! __('ui.about.skillsTitle') !!}</h2>
        <p class="section-subtitle" data-i18n="about.skillsSubtitle">{{ __('ui.about.skillsSubtitle') }}</p>
      </div>
      <div class="row g-4" data-render="skills"></div>
      <div class="tools-grid mt-5" data-render="tools"></div>
    </div>
  </section>

  @include('sections.experience')

  @include('sections.education', ['alt' => true])

  @include('sections.cta')
@endsection
