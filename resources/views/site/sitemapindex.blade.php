{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($languages as $language)
    <sitemap>
        <loc>{{ route('sitemap', $language->language_code) }}</loc>
    </sitemap>
    @endforeach
</sitemapindex>