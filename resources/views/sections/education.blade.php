{{-- Params: $alt (bool, optional) adds the alternate background --}}
<!-- ===== EDUCATION ===== -->
<section class="section{{ ($alt ?? false) ? ' section--alt' : '' }}" id="education" aria-labelledby="eduTitle">
  <div class="container">
    <div class="edu-layout">
      <div class="edu-intro reveal" data-reveal="left">
        <div class="section-head">
          <span class="eyebrow" data-i18n="edu.eyebrow">{{ __('ui.edu.eyebrow') }}</span>
          <h2 class="section-title" id="eduTitle" data-i18n-html="edu.title">{!! __('ui.edu.title') !!}</h2>
          <p class="section-subtitle" data-i18n="edu.subtitle">{{ __('ui.edu.subtitle') }}</p>
        </div>
        <div class="card-ui edu-note">
          <i class="fa-solid fa-lightbulb" aria-hidden="true"></i>
          <p data-i18n="edu.note">{{ __('ui.edu.note') }}</p>
        </div>
      </div>
      <ul class="edu-list" data-render="education"></ul>
    </div>
    <div class="section-cta">
      <a class="btn-ui btn-ui--primary btn-ui--lg" href="{{ localized_route('contact') }}"><span data-i18n="edu.cta">{{ __('ui.edu.cta') }}</span><i class="fa-solid fa-arrow-right dir-icon" aria-hidden="true"></i></a>
    </div>
  </div>
</section>
