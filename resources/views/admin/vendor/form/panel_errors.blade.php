<ul class="list-group form-errors" {!! isset($errors) && $errors->count() ? '' : 'style="display:none"' !!}>
    @foreach($errors->all() as $error)
        <li class="list-group-item list-group-item-danger">
            <i class="fa fa-warning"></i>
            {{ $error }}
        </li>
    @endforeach
</ul>