{{-- schema.org Person (home and about pages). --}}
@push('structured-data')
<script type="application/ld+json" nonce="{{ Vite::cspNonce() }}">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Person',
    'name' => $profile['name'],
    'url' => localized_route('home'),
    'image' => $profile['photo'],
    'jobTitle' => config('portfolio.profile.job_title'),
    'email' => 'mailto:'.$profile['email'],
    'address' => ['@type' => 'PostalAddress', 'addressLocality' => $profile['location']],
    'sameAs' => array_values(config('portfolio.profile.socials')),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
</script>
@endpush
