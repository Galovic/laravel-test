<span class="help-block text-danger" {{ $errors->has($name) ? '' : 'style="display:none"' }}>{{ $errors->first($name) }}</span>