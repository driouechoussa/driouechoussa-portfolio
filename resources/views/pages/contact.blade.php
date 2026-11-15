@extends('layouts.app')

@section('page', 'contact')
@section('title', __('ui.meta.contact'))
@section('description', __('ui.metaDesc.contact'))

@section('content')

  @include('sections.page-hero', ['page' => 'contact', 'i18n' => 'contact'])

  <section class="section pt-4" aria-label="{{ __('ui.nav.contact') }}" data-i18n-aria="nav.contact">
    <div class="container">
      <div class="row g-4 g-lg-5">
        <div class="col-lg-5">
          <div class="d-grid gap-3">
            <a class="card-ui contact-card reveal" data-profile-href="email" href="{{ $profile['href']['email'] }}">
              <span class="service-icon" aria-hidden="true"><i class="fa-solid fa-envelope"></i></span>
              <span><small data-i18n="contact.email">{{ __('ui.contact.email') }}</small><strong data-profile="email">{{ $profile['email'] }}</strong></span>
            </a>
            <a class="card-ui contact-card reveal" data-profile-href="phone" href="{{ $profile['href']['phone'] }}">
              <span class="service-icon" aria-hidden="true"><i class="fa-solid fa-phone"></i></span>
              <span><small data-i18n="contact.phone">{{ __('ui.contact.phone') }}</small><strong data-profile="phone" dir="ltr">{{ $profile['phone'] }}</strong></span>
            </a>
            <div class="card-ui contact-card reveal">
              <span class="service-icon" aria-hidden="true"><i class="fa-solid fa-location-dot"></i></span>
              <span><small data-i18n="contact.location">{{ __('ui.contact.location') }}</small><strong data-profile="location">{{ $profile['location'] }}</strong></span>
            </div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="card-ui form-card reveal" data-reveal="right">
            <h2 class="h3 mb-2" data-i18n="contact.formTitle">{{ __('ui.contact.formTitle') }}</h2>
            <p data-i18n="contact.formText">{{ __('ui.contact.formText') }}</p>
            @if (session('status'))
              <div class="alert-ui alert-ui--success mt-3" role="status"><i class="fa-solid fa-circle-check" aria-hidden="true"></i> {{ session('status') }}</div>
            @endif
            <form id="contactForm" class="row g-3 mt-1" method="POST" action="{{ localized_route('contact.store') }}" novalidate>
              @csrf
              {{-- Honeypot: hidden from people, often filled in by spam bots. --}}
              <div class="hp-field" aria-hidden="true">
                <label for="website">Website</label>
                <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
              </div>
              <div class="col-md-6 field @error('name') has-error @enderror">
                <label class="form-label-ui" for="name" data-i18n="form.name">{{ __('ui.form.name') }}</label>
                <input class="input-ui" id="name" name="name" type="text" autocomplete="name" required data-i18n-placeholder="form.namePh" placeholder="{{ __('ui.form.namePh') }}" value="{{ old('name') }}" aria-describedby="nameError" @error('name') aria-invalid="true" @enderror>
                <span class="field-error" id="nameError" aria-live="polite">@error('name'){{ $message }}@enderror</span>
              </div>
              <div class="col-md-6 field @error('email') has-error @enderror">
                <label class="form-label-ui" for="email" data-i18n="form.email">{{ __('ui.form.email') }}</label>
                <input class="input-ui" id="email" name="email" type="email" autocomplete="email" required data-i18n-placeholder="form.emailPh" placeholder="{{ __('ui.form.emailPh') }}" value="{{ old('email') }}" aria-describedby="emailError" @error('email') aria-invalid="true" @enderror>
                <span class="field-error" id="emailError" aria-live="polite">@error('email'){{ $message }}@enderror</span>
              </div>
              <div class="col-12 field">
                <label class="form-label-ui" for="subject" data-i18n="form.subject">{{ __('ui.form.subject') }}</label>
                <select class="input-ui" id="subject" name="subject">
                  <option value="project" @selected(old('subject') === 'project') data-i18n="form.opt.project">{{ __('ui.form.opt.project') }}</option>
                  <option value="freelance" @selected(old('subject') === 'freelance') data-i18n="form.opt.freelance">{{ __('ui.form.opt.freelance') }}</option>
                  <option value="job" @selected(old('subject') === 'job') data-i18n="form.opt.job">{{ __('ui.form.opt.job') }}</option>
                  <option value="other" @selected(old('subject') === 'other') data-i18n="form.opt.other">{{ __('ui.form.opt.other') }}</option>
                </select>
              </div>
              <div class="col-12 field @error('message') has-error @enderror">
                <label class="form-label-ui" for="message" data-i18n="form.message">{{ __('ui.form.message') }}</label>
                <textarea class="input-ui" id="message" name="message" required minlength="20" data-i18n-placeholder="form.messagePh" placeholder="{{ __('ui.form.messagePh') }}" maxlength="5000" aria-describedby="messageError" @error('message') aria-invalid="true" @enderror>{{ old('message') }}</textarea>
                <span class="field-error" id="messageError" aria-live="polite">@error('message'){{ $message }}@enderror</span>
              </div>
              <div class="col-12 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <p class="small mb-0"><span data-i18n="form.note">{{ __('ui.form.note') }}</span> <a href="{{ localized_route('privacy') }}" data-i18n="form.privacyLink">{{ __('ui.form.privacyLink') }}</a></p>
                <button class="btn-ui btn-ui--primary btn-ui--lg" type="submit"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span data-i18n="form.send">{{ __('ui.form.send') }}</span></button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
