@php
    use App\Support\Locale;
    use App\Support\PortfolioData;

    $locale = Locale::current();
    $appData ??= PortfolioData::make();
    $alternates = Locale::alternates();
    $canonical = $alternates[$locale] ?? url()->current();
    $title = trim($__env->yieldContent('title', __('ui.meta.home')));
    $description = trim($__env->yieldContent('description', __('ui.metaDesc.home')));
    $ogImage = trim($__env->yieldContent('og_image', asset('images/og-image.png')));
    $nonce = Vite::cspNonce();
    $i18n = [$locale => trans('ui')] + ($locale === Locale::DEFAULT ? [] : [Locale::DEFAULT => trans('ui', [], Locale::DEFAULT)]);
@endphp
<!doctype html>
<html lang="{{ $locale }}" dir="{{ Locale::dir() }}" data-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title }}</title>
  <meta name="description" content="{{ $description }}">
  <meta name="author" content="{{ config('portfolio.profile.name.en') }}">
  <meta name="theme-color" content="#17130e">
  @hasSection('robots')<meta name="robots" content="@yield('robots')">@endif

  <link rel="canonical" href="{{ $canonical }}">
  @foreach ($alternates as $lang => $url)
  <link rel="alternate" hreflang="{{ $lang }}" href="{{ $url }}">
  @endforeach
  @isset($alternates[Locale::DEFAULT])
  <link rel="alternate" hreflang="x-default" href="{{ $alternates[Locale::DEFAULT] }}">
  @endisset

  {{-- Social sharing --}}
  <meta property="og:site_name" content="{{ config('portfolio.profile.name.'.$locale) }}">
  <meta property="og:type" content="@yield('og_type', 'website')">
  <meta property="og:title" content="{{ $title }}">
  <meta property="og:description" content="{{ $description }}">
  <meta property="og:url" content="{{ $canonical }}">
  <meta property="og:image" content="{{ $ogImage }}">
  <meta property="og:locale" content="{{ Locale::og() }}">
  @foreach (array_diff(array_keys($alternates), [$locale]) as $lang)
  <meta property="og:locale:alternate" content="{{ Locale::og($lang) }}">
  @endforeach
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="{{ $title }}">
  <meta name="twitter:description" content="{{ $description }}">
  <meta name="twitter:image" content="{{ $ogImage }}">

  <link rel="icon" href="{{ asset('images/icons/favicon.svg') }}" type="image/svg+xml">
  <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="48x48">
  <link rel="apple-touch-icon" href="{{ asset('images/icons/apple-touch-icon.png') }}">

  {{-- Applies the saved theme before the first paint (no flash of the wrong theme). --}}
  <script nonce="{{ $nonce }}">
    (function () {
      var root = document.documentElement, theme = null;
      root.classList.add("js");
      try { theme = localStorage.getItem("portfolio-theme"); } catch (e) {}
      if (theme !== "light" && theme !== "dark") {
        theme = window.matchMedia && window.matchMedia("(prefers-color-scheme: light)").matches ? "light" : "dark";
      }
      root.setAttribute("data-theme", theme);
    })();
  </script>

  {{-- Fonts: Sora + Manrope (EN/FR), Cairo (AR) --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap">

  @vite('resources/css/app.css')

  @stack('structured-data')

  @if ($domain = config('portfolio.analytics.plausible_domain'))
  <script defer nonce="{{ $nonce }}" data-domain="{{ $domain }}" src="{{ config('portfolio.analytics.plausible_host') }}/js/script.js"></script>
  @endif
</head>
<body data-page="@yield('page', 'home')" data-root="{{ Locale::route('home') }}/">
  <a class="skip-link" href="#main">{{ __('ui.nav.skip') }}</a>

  <div id="site-header"></div>

  <main id="main">
    @yield('content')
  </main>

  <div id="site-footer"></div>

  {{-- Content and UI strings for the front-end scripts. --}}
  <script nonce="{{ $nonce }}">
    window.App = window.App || {};
    window.App.data = @json($appData);
    window.App.i18n = @json($i18n);
    window.App.alternates = @json((object) $alternates);
  </script>
  @vite('resources/js/app.js')
</body>
</html>
