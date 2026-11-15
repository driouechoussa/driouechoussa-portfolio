@extends('layouts.app')

@section('page', 'error')
@section('title', __('ui.error.'.$code.'.title').' — '.$profile['name'])
@section('description', __('ui.error.'.$code.'.text'))
@section('robots', 'noindex')

@section('content')
  <section class="error-page">
    <div class="container">
      <p class="error-code" aria-hidden="true">{{ $code }}</p>
      <h1>{{ __('ui.error.'.$code.'.title') }}</h1>
      <p>{{ __('ui.error.'.$code.'.text') }}</p>
      <a class="btn-ui btn-ui--primary btn-ui--lg" href="{{ localized_route('home') }}">
        <i class="fa-solid fa-house" aria-hidden="true"></i><span>{{ __('ui.error.home') }}</span>
      </a>
    </div>
  </section>
@endsection
