<div class="form-group clearfix {{ $errors->has(array_get($params, 'name')) ? 'has-error' : '' }}">
	<label class="col-sm-2 control-label">{{ __(array_get($params, 'label')) }}</label>
	<div class="col-sm-10">
		<textarea
			id="{{ array_get($params, 'name') }}"
			name="{{ array_get($params, 'name') }}"
			rows="{{ array_get($params, 'rows') }}"
			class="form-control"
			{{ array_get($params, 'required', false) ? 'required' : '' }}>{{ old(array_get($params, 'name')) ?: array_get($params, 'value') }}</textarea>
		<small class="text-danger">{{ $errors->first(array_get($params,'name')) }}</small>
	</div>
</div>
