<div class="form-group clearfix {{ $errors->has(array_get($params, 'name')) ? 'has-error' : '' }}">
	<label class="col-sm-2 control-label">{{ __(array_get($params, 'label')) }}</label>
	<div class="col-sm-10">
		<select
			id="{{ array_get($params, 'name') }}"
			name="{{ array_get($params, 'name') }}"
			class="form-control"
			value="{{ old(array_get($params, 'name')) ?: array_get($params, 'value') }}"
			{{ array_get($params, 'required', false) ? 'required' : '' }}">
		@foreach(array_get($params, 'options') as $key => $label)
			<option value="{{ $key }}"{{ $key == (old(array_get($params, 'name')) ?: array_get($params, 'value')) ? ' selected' : ''}}>{{ $label }}</option>
		@endforeach
		</select>
		<small class="text-danger">{{ $errors->first(array_get($params,'name')) }}</small>
	</div>
</div>
