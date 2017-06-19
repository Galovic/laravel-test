

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="id">ID</label>
            <input class="form-control" name="id" type="text" value="{{ $module['id'] or old('id') }}" id="id">
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="class">Class</label>
            <input class="form-control" name="class" type="text" value="{{ $module['class'] or old('class') }}" id="class">
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="margin">Margin</label>
            <input class="form-control" name="margin" type="text" value="{{ $module['margin'] or old('margin') }}" id="margin">
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="padding">Padding</label>
            <input class="form-control" name="padding" type="text" value="{{ $module['padding'] or old('padding') }}" id="padding">
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="border">Border</label>
            <input class="form-control" name="border" type="text" value="{{ $module['border'] or old('border') }}" id="border">
        </div>
    </div>
</div>