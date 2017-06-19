<h2 class="text-center">Články</h2>

<div class="list-group">
    @foreach($articles as $article)
        <a href="{{ $article->full_url }}" class="list-group-item">
            <img src="{{ $article->thumbnail_url }}" width="100">
            <div style="display: inline-block;">
            <strong>{{ $article->title }}</strong>
            <p>
                {{ $article->perex }}
            </p>
            </div>
        </a>
    @endforeach
</div>