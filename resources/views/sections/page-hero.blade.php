{{-- Params: $page (nav key, e.g. "about"), $i18n (translation prefix, e.g. "about") --}}
<!-- ===== PAGE HERO ===== -->
<section class="page-hero" aria-labelledby="pageTitle">
  <div class="container">
    <nav class="breadcrumb-ui" data-i18n-aria="common.breadcrumb" aria-label="{{ __('ui.common.breadcrumb') }}">
      <a href="{{ localized_route('home') }}" data-i18n="nav.home">{{ __('ui.nav.home') }}</a>
      <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
      <span aria-current="page" data-i18n="nav.{{ $page }}">{{ __('ui.nav.'.$page) }}</span>
    </nav>
    <div class="hero-enter">
      <span class="eyebrow" data-i18n="{{ $i18n }}.eyebrow">{{ __('ui.'.$i18n.'.eyebrow') }}</span>
      <h1 id="pageTitle" data-i18n-html="{{ $i18n }}.title">{!! __('ui.'.$i18n.'.title') !!}</h1>
      <p class="lead-text" data-i18n="{{ $i18n }}.lead">{{ __('ui.'.$i18n.'.lead') }}</p>
    </div>
  </div>
</section>
