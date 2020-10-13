<div class="form-group clearfix {{ $errors->has(array_get($params, 'name')) ? 'has-error' : '' }}">
	<label class="col-sm-2 control-label">{{ __(array_get($params, 'label')) }}</label>
	<div class="col-sm-10">
		{{ old(array_get($params, 'name')) ?: array_get($params, 'value') }}
		<input
			type="hidden"
			id="{{ array_get($params, 'name') }}"
			name="{{ array_get($params, 'name') }}"
			class="form-control"
			value="{{ old(array_get($params, 'name')) ?: array_get($params, 'value') }}"
			{{ array_get($params, 'required', false) ? 'required' : '' }}">
		<small class="text-danger">{{ $errors->first(array_get($params,'name')) }}</small>
	</div>
</div>
