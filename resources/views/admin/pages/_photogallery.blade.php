<!-- Media library -->
<div class="panel panel-white">
    <div class="panel-heading">
        <h6 class="panel-title text-semibold">Fotografie</h6>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
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
                    <a href="{{ $p['image']['big'] }}" data-popup="lightbox">
                        <img src="{{ $p['image']['small'] }}" alt="" class="img-rounded img-preview">
                    </a>
                </td>
                <td><a href="#" class="editable" data-pk="{{ $p['id'] }}" data-name="title" data-title="Popisek">{{ $p['title'] }}</a></td>
                <td><a href="#" class="editable" data-pk="{{ $p['id'] }}" data-name="author" data-title="Autor fotografie">{{ $p['author'] }}</a></td>
                <td>
                    <ul class="list-condensed list-unstyled no-margin">
                        <li><span class="text-semibold">Vloženo:</span> {{ $p['created_at'] }}</li>
                        <li><span class="text-semibold">Size:</span> {{ $p['size'] }} kB</li>
                        <li><span class="text-semibold">Format:</span> .{{ $p['type'] }}</li>
                    </ul>
                </td>
                <td class="text-center">
                    <ul class="icons-list">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="icon-menu9"></i>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-right">
                                <li><a href="javascript:deletePhoto({{ $p['id'] }})"><i class="icon-bin"></i> Smazat</a></li>
                            </ul>
                        </li>
                    </ul>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<!-- /media library -->
