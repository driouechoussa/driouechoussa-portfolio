{{-- Params: $alt (bool, optional) adds the alternate background --}}
<!-- ===== EXPERIENCE (snake timeline) ===== -->
<section class="section{{ ($alt ?? false) ? ' section--alt' : '' }}" id="experience" aria-labelledby="xpTitle">
  <div class="container">
    <div class="section-head section-head--center reveal">
      <span class="eyebrow" data-i18n="xp.eyebrow">{{ __('ui.xp.eyebrow') }}</span>
      <h2 class="section-title" id="xpTitle" data-i18n-html="xp.title">{!! __('ui.xp.title') !!}</h2>
      <p class="section-subtitle" data-i18n="xp.subtitle">{{ __('ui.xp.subtitle') }}</p>
    </div>
    <div class="xp-snake">
      <div class="xp-grid" data-render="experience" data-limit="6"></div>
    </div>
  </div>
</section>
