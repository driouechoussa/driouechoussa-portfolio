@extends('layouts.app')

@section('page', 'blogs')
@section('title', __('ui.meta.blogs'))
@section('description', __('ui.metaDesc.blogs'))

@section('content')

  @include('sections.page-hero', ['page' => 'blogs', 'i18n' => 'blogsPage'])

  <section class="section pt-4" data-i18n-aria="blogsPage.listAria" aria-label="{{ __('ui.blogsPage.listAria') }}">
    <div class="container">
      <div class="filter-bar reveal">
        <div class="chips" data-filter-for="posts" role="group" data-i18n-aria="common.filter" aria-label="{{ __('ui.common.filter') }}"></div>
        <div class="search-field">
          <label class="visually-hidden" for="postSearch" data-i18n="blogsPage.search">{{ __('ui.blogsPage.search') }}</label>
          <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
          <input class="input-ui" id="postSearch" type="search" data-search-for="posts" data-i18n-placeholder="blogsPage.search" placeholder="{{ __('ui.blogsPage.search') }}" autocomplete="off">
        </div>
      </div>
      <div class="row g-4" data-render="posts"></div>
      <div class="empty-state is-hidden" data-empty-for="posts"><i class="fa-regular fa-file-lines" aria-hidden="true"></i><p data-i18n="common.empty">{{ __('ui.common.empty') }}</p></div>
    </div>
  </section>

  @include('sections.cta')
@endsection
