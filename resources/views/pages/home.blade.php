@extends('layouts.app')

@section('page', 'home')
@section('title', __('ui.meta.home'))
@section('description', __('ui.metaDesc.home'))

@include('partials.schema.person')

@section('content')

  <!-- ===== HERO — identity card ===== -->
  <section class="hero" id="home" aria-labelledby="heroName">
    <div class="container">
      <div class="id-card hero-enter">
        <div class="id-photo">
          <img data-profile-src="photo" draggable="false" src="{{ $profile['photo'] }}" data-i18n-alt="hero.photoAlt" alt="{{ __('ui.hero.photoAlt') }}" width="480" height="480" fetchpriority="high">
        </div>
        <p class="id-handle" dir="ltr" data-profile="handle">{{ $profile['handle'] }}</p>
        <h1 class="id-name" id="heroName" data-profile="name">{{ $profile['name'] }}</h1>
        <p class="id-title" data-i18n="hero.title">{{ __('ui.hero.title') }}</p>
        <div class="socials">
          @foreach (\App\Support\ContactDetails::SOCIALS as $network => $social)
            @isset($profile['socials'][$network])
              <a class="social-link" data-profile-href="{{ $network }}" href="{{ $profile['socials'][$network] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['label'] }}"><i class="{{ $social['icon'] }}" aria-hidden="true"></i></a>
            @endisset
          @endforeach
        </div>
        <p class="id-desc" data-i18n="hero.desc">{{ __('ui.hero.desc') }}</p>
        <div class="id-actions">
          <a class="btn-ui btn-ui--primary btn-ui--lg" data-profile-href="cv" href="{{ $profile['href']['cv'] }}" download><i class="fa-solid fa-download" aria-hidden="true"></i><span data-i18n="hero.cv">{{ __('ui.hero.cv') }}</span></a>
          <a class="btn-ui btn-ui--outline btn-ui--lg" href="{{ localized_route('projects.index') }}"><span data-i18n="hero.works">{{ __('ui.hero.works') }}</span><i class="fa-solid fa-arrow-right dir-icon" aria-hidden="true"></i></a>
        </div>
        <dl class="id-stats">
          <div><dt data-count="{{ config('portfolio.stats.years') }}"><span class="count-num">{{ config('portfolio.stats.years') }}</span>+</dt><dd data-i18n="stats.years">{{ __('ui.stats.years') }}</dd></div>
          <div><dt data-count="{{ config('portfolio.stats.projects') }}"><span class="count-num">{{ config('portfolio.stats.projects') }}</span>+</dt><dd data-i18n="stats.projects">{{ __('ui.stats.projects') }}</dd></div>
          <div><dt data-count="{{ config('portfolio.stats.clients') }}"><span class="count-num">{{ config('portfolio.stats.clients') }}</span>+</dt><dd data-i18n="stats.clients">{{ __('ui.stats.clients') }}</dd></div>
        </dl>
      </div>
    </div>
  </section>

  <!-- ===== PROJECTS ===== -->
  <section class="section" id="projects" aria-labelledby="projectsTitle">
    <div class="container">
      <div class="section-head section-head--split reveal">
        <div>
          <span class="eyebrow" data-i18n="projects.eyebrow">{{ __('ui.projects.eyebrow') }}</span>
          <h2 class="section-title" id="projectsTitle" data-i18n-html="projects.title">{!! __('ui.projects.title') !!}</h2>
          <p class="section-subtitle" data-i18n="projects.subtitle">{{ __('ui.projects.subtitle') }}</p>
        </div>
      </div>
      <div class="row g-4" data-render="projects" data-limit="6"></div>
      <div class="section-cta">
        <a class="btn-ui btn-ui--primary btn-ui--lg" href="{{ localized_route('projects.index') }}"><span data-i18n="projects.cta">{{ __('ui.projects.cta') }}</span><i class="fa-solid fa-arrow-right dir-icon" aria-hidden="true"></i></a>
      </div>
    </div>
  </section>

  @include('sections.education')


  @include('sections.experience', ['alt' => true])

  <!-- ===== BLOG ===== -->
  <section class="section" id="blog" aria-labelledby="blogTitle">
    <div class="container">
      <div class="section-head section-head--split reveal">
        <div>
          <span class="eyebrow" data-i18n="blog.eyebrow">{{ __('ui.blog.eyebrow') }}</span>
          <h2 class="section-title" id="blogTitle" data-i18n-html="blog.title">{!! __('ui.blog.title') !!}</h2>
          <p class="section-subtitle" data-i18n="blog.subtitle">{{ __('ui.blog.subtitle') }}</p>
        </div>
      </div>
      <div class="row g-4" data-render="posts" data-limit="3"></div>
      <div class="section-cta">
        <a class="btn-ui btn-ui--primary btn-ui--lg" href="{{ localized_route('posts.index') }}"><span data-i18n="blog.cta">{{ __('ui.blog.cta') }}</span><i class="fa-solid fa-arrow-right dir-icon" aria-hidden="true"></i></a>
      </div>
    </div>
  </section>

  @include('sections.cta')
@endsection
