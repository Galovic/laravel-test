<div class="col-md-12 clearfix">
    <h2>{{ $article->title }}</h2>
    <p>{{ $article->perex }}</p>
    <a href="{{ route('articles.detail', $article->url) }}" class="p-read-more hvr-reveal">Read more</a>
</div>
