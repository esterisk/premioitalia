<div class="form-group clearfix {{ $errors->has(array_get($params, 'name')) ? 'has-error' : '' }}">
	<label class="col-sm-2 control-label">{{ __(array_get($params, 'label')) }}</label>
	<div class="col-sm-10">
		<input
			type="{{ array_get($params, 'type', 'text') }}"
			id="{{ array_get($params, 'name') }}"
			name="{{ array_get($params, 'name') }}"
			class="form-control"
			value="{{ old(array_get($params, 'name')) ?: array_get($params, 'value') }}"
			placeholder="{{ __(array_get($params, 'label')) }}"
			{{ array_get($params, 'required', false) ? 'required' : '' }}">
		@if($info = array_get($params, 'info')
		<small id="emailHelp" class="form-text text-muted">{{ $info }}</small>
		@endif
		<small class="text-danger">{{ $errors->first(array_get($params,'name')) }}</small>
	</div>
</div>
