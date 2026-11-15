@extends('layouts.app')

@section('page', 'projects')
@section('title', __('ui.meta.projects'))
@section('description', __('ui.metaDesc.projects'))

@section('content')

  @include('sections.page-hero', ['page' => 'projects', 'i18n' => 'projectsPage'])

  <section class="section pt-4" aria-label="{{ __('ui.projectsPage.listAria') }}" data-i18n-aria="projectsPage.listAria">
    <div class="container">
      <div class="filter-bar reveal">
        <div class="chips" data-filter-for="projects" role="group" data-i18n-aria="common.filter" aria-label="{{ __('ui.common.filter') }}"></div>
      </div>
      <div class="row g-4" data-render="projects"></div>
      <div class="empty-state is-hidden" data-empty-for="projects"><i class="fa-regular fa-folder-open" aria-hidden="true"></i><p data-i18n="common.empty">{{ __('ui.common.empty') }}</p></div>
    </div>
  </section>

  @include('sections.cta')
@endsection
