<div class="col-md-4 eq-height clearfix">
    <h2>{{ $service->title }}</h2>
    <p>{{ $service->perex }}</p>
    <a href="{{ route('services.detail', $service->url) }}" class="p-read-more hvr-reveal">Read more</a>
</div>
