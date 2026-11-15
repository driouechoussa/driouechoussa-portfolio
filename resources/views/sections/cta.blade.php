<!-- ===== CTA ===== -->
<section class="section pb-0" aria-labelledby="ctaTitle">
  <div class="container">
    <div class="cta-banner reveal" data-reveal="zoom">
      <h2 id="ctaTitle" data-i18n="cta.title">{{ __('ui.cta.title') }}</h2>
      <p data-i18n="cta.text">{{ __('ui.cta.text') }}</p>
      <div class="cta-actions">
        <a class="btn-ui btn-ui--dark btn-ui--lg" href="{{ localized_route('contact') }}"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span data-i18n="cta.start">{{ __('ui.cta.start') }}</span></a>
        <a class="btn-ui btn-ui--line btn-ui--lg" data-profile-href="email" href="{{ $profile['href']['email'] }}"><i class="fa-regular fa-envelope" aria-hidden="true"></i><span data-i18n="cta.email">{{ __('ui.cta.email') }}</span></a>
      </div>
    </div>
  </div>
</section>
