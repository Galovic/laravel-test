@if ($isProduction)
User-agent: *
Disallow: /admin
Sitemap: {{ route('sitemap.index') }}
@else
Disallow: /
@endif