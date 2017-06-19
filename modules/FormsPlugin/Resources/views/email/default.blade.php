
@foreach($values as $fieldValue)

    <strong>{{ $fieldValue->name }}:</strong><br>
    {{ $fieldValue->value }}<br>
    <br>

@endforeach

<hr>
<strong>Odesláno z formuláře na stránce: </strong>
<a href="{!! $previousUrl !!}" target="_blank">{{ $previousUrl }}</a>