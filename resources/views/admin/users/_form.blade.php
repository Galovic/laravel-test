<div class="tabbable tab-content-bordered">
    <ul class="nav nav-tabs nav-tabs-highlight">
        <li class="active">
            <a href="#tab_details" data-toggle="tab" aria-expanded="true">Základní informace</a>
        </li>
        <li>
            <a href="#tab_roles" data-toggle="tab" aria-expanded="true">Role</a>
        </li>
    </ul>
    <div class="tab-content">

        <div class="tab-pane has-padding active" id="tab_details">
            <div class="form-group required {{ $errors->has($name = 'firstname') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Jméno') !!}
                {!! Form::text($name, null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => 255
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group required {{ $errors->has($name = 'lastname') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Příjmení') !!}
                {!! Form::text($name, null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => 255
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group required {{ $errors->has($name = 'username') ? 'has-error' : '' }}">
                {!! Form::label($name,'Přihlašovací jméno') !!}
                {!! Form::text($name, null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => 50
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group required {{ $errors->has($name = 'email') ? 'has-error' : '' }}">
                {!! Form::label($name,'Email') !!}
                {!! Form::email($name, null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => 255
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group required {{ $errors->has($name = 'password') ? 'has-error' : '' }}">
                {!! Form::label($name,'Heslo') !!}
                {!! Form::password($name, [
                    'class' => 'form-control'
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group required {{ $errors->has($name = 'password_confirmation') ? 'has-error' : '' }}">
                {!! Form::label($name,'Potvrzení hesla') !!}
                {!! Form::password($name, [
                    'class' => 'form-control'
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>


            <div class="checkbox checkbox-switchery">
                <label>
                    {!! Form::checkbox('enabled', 1, null, ['id' => 'input-enabled']) !!}
                    Povoleno
                </label>
            </div>

        </div>


        <div class="tab-pane has-padding" id="tab_roles">
            <table class="table table-hover" id="tbl-roles">
                <tbody>
                <tr>
                    <th width="50"></th>
                    <th>Název</th>
                    <th>Popis</th>
                </tr>
                @foreach($roles as $role)
                <tr>
                    <td>
                        {{ Form::checkbox('role[]', $role->id, isset($user) && $user->roles->find($role->id), [
                            'class' => 'switchery'
                        ]) }}
                    </td>
                    <td>{{ $role->display_name }}</td>
                    <td>{{ $role->description }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>

@push('script')
{{ Html::script( url('js/switchery.js') ) }}
{{ Html::script( url('js/bootstrap-maxlength.js') ) }}
<script>
    (function($){

        $('.switchery').add('#input-enabled').each(function(){
            new Switchery(this);
        });

        $('.maxlength').maxlength();
    })(jQuery)
</script>
@endpush