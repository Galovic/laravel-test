<!-- Media library -->
<div class="panel panel-white">
    <div class="panel-heading">
        <h6 class="panel-title text-semibold">Fotografie</h6>
    </div>

    <table class="table table-striped media-library table-lg">
        <thead>
        <tr>
            <th width="150">Náhled</th>
            <th>Popisek</th>
            <th>Autor</th>
            <th width="240">Informace</th>
            <th width="80" class="text-center">Akce</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($photos as $p)
            <tr>
                <td>
                    <a href="{{ $photogallery->photos_url . '/big/' . $p->image  }}" data-popup="lightbox">
                        <img src="{{ $photogallery->photos_url . '/small/' . $p->image }}" alt="" class="img-rounded img-preview">
                    </a>
                </td>
                <td><a href="#" class="editable" data-pk="{{ $p->id }}" data-name="title" data-title="Popisek">{{ $p['title'] }}</a></td>
                <td><a href="#" class="editable" data-pk="{{ $p->id }}" data-name="author" data-title="Autor fotografie">{{ $p->author }}</a></td>
                <td>
                    <ul class="list-condensed list-unstyled no-margin">
                        <li>
                            <span class="text-semibold">Vloženo:</span>
                            {{ $p->created_at->format('j.n.Y H:i:s') }}
                        </li>
                        <li><span class="text-semibold">Velikost:</span>
                            {{ number_format($p->size, 2, ',', ' ') }} kB
                        </li>
                        <li>
                            <span class="text-semibold">Formát:</span>
                            {{ strtoupper($p->type) }}
                        </li>
                    </ul>
                </td>
                <td class="text-center">

                    <a class="delete-photo" href="{{ route('admin.photogalleries.photo.delete', $p->id) }}">
                        <i class="fa fa-trash"></i> Smazat
                    </a>

                </td>
            </tr>
        @endforeach

        @if(!$photos->count())
            <tr><td colspan="5" class="text-center"><em>Nenahrány žádné fotografie.</em></td></tr>
        @endif

        </tbody>
    </table>
</div>
