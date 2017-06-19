{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
    <url>
        <loc>{{ url('/', $language->language_code) }}</loc>
        <priority>1.0</priority>
        @foreach($alternateLanguages as $alternateLanguage)
            <xhtml:link
                    rel="alternate"
                    hreflang="{{ $alternateLanguage->language_code }}"
                    href="{{ url('/', $alternateLanguage->language_code) }}" />
        @endforeach
    </url>

    @foreach ($urlSet as $url)
        <url>
            <loc>{{ $url->loc }}</loc>
            <lastmod>{{ $url->lastmod->tz('UTC')->toAtomString() }}</lastmod>
            <changefreq>{{ $url->changefreq }}</changefreq>
            <priority>{{ $url->priority }}</priority>
        </url>
    @endforeach
</urlset>