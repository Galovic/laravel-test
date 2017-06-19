<div class="form-group" style="display: none">
    <div class="col-md-12">
        {{ Form::email("email_honeypot", null, [
            'class' => 'form-control',
            'placeholder' => 'Honeypot'
         ]) }}
    </div>
</div>