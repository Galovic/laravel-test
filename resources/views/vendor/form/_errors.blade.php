@if (count($errors) > 0)
    <div class="alert alert-danger alert-dismissable">
        @foreach($errors->all() as $error)
            <i class="fa fa-warning"></i>
            {{ $error }}
            <br>
        @endforeach
    </div>
@endif

@if (isset($status))
    <div class="alert alert-info alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4>
            <i class="icon fa fa-info"></i> Info
        </h4>
        <li>{{ $status }}</li>
    </div>
@endif
