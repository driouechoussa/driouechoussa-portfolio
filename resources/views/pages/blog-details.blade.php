@extends('layouts.app')

@php
    $title = $post->localized('title');
    $excerpt = $post->localized('excerpt');
@endphp

@section('page', 'blog-details')
@section('title', $title.' — '.$profile['name'])
@section('description', $excerpt)
@section('og_type', 'article')

@push('structured-data')
<meta property="article:published_time" content="{{ $post->published_at->toIso8601String() }}">
<meta property="article:modified_time" content="{{ $post->updated_at->toIso8601String() }}">
<script type="application/ld+json" nonce="{{ Vite::cspNonce() }}">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BlogPosting',
    'headline' => $title,
    'description' => $excerpt,
    'url' => localized_route('posts.show', $post),
    'mainEntityOfPage' => localized_route('posts.show', $post),
    'image' => App\Support\Media::url($post->image),
    'datePublished' => $post->published_at->toDateString(),
    'dateModified' => $post->updated_at->toDateString(),
    'inLanguage' => app()->getLocale(),
    'author' => ['@type' => 'Person', 'name' => $profile['name'], 'url' => localized_route('home')],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
</script>
@endpush

@section('content')

  <div data-render="post-detail" data-id="{{ $post->slug }}">
    {{-- Server-rendered article for search engines and link previews; replaced by the scripts. --}}
    <article class="ssr-fallback container py-5">
      <h1>{{ $title }}</h1>
      <p><time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->toDateString() }}</time></p>
      <p>{{ $excerpt }}</p>
      @foreach ($post->body as $block)
        @php($text = $block['type'] === 'code' ? $block['text'] : App\Models\Post::pickLocale($block['text']))
        @switch($block['type'])
          @case('h2')
            <h2>{{ $text }}</h2>
            @break
          @case('quote')
            <blockquote>{{ $text }}</blockquote>
            @break
          @case('code')
            <pre><code>{{ $text }}</code></pre>
            @break
          @case('ul')
            <ul>@foreach ((array) $text as $item)<li>{{ $item }}</li>@endforeach</ul>
            @break
          @default
            <p>{{ $text }}</p>
        @endswitch
      @endforeach
    </article>
  </div>

  <!-- ===== RELATED ===== -->
  <section class="section" aria-labelledby="relatedTitle">
    <div class="container">
      <div class="section-head section-head--split reveal">
        <div>
          <span class="eyebrow" data-i18n="bd.relatedEyebrow">{{ __('ui.bd.relatedEyebrow') }}</span>
          <h2 class="section-title" id="relatedTitle" data-i18n-html="bd.related">{!! __('ui.bd.related') !!}</h2>
        </div>
        <a class="btn-ui btn-ui--outline" href="{{ localized_route('posts.index') }}"><span data-i18n="blog.cta">{{ __('ui.blog.cta') }}</span><i class="fa-solid fa-arrow-right dir-icon" aria-hidden="true"></i></a>
      </div>
      <div class="row g-4" data-render="posts" data-limit="3" data-exclude-current></div>
    </div>
  </section>
@endsection
