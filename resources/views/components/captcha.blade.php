<div class="form-group clearfix {{ $errors->has(array_get($params, 'name')) ? 'has-error' : '' }}">
	<label class="col-sm-2 control-label">{{ __(array_get($params, 'label', 'Non sono un robot')) }}</label>
	<div class="col-sm-3">
		@captcha
	</div>
	<div class="col-sm-7">
		<input
			type="text"
			id="{{ array_get($params, 'name', 'captcha') }}"
			name="{{ array_get($params, 'name', 'captcha') }}"
			class="form-control"
			value="{{ old(array_get($params, 'name', 'captcha')) ?: array_get($params, 'value') }}"
			placeholder="{{ __(array_get($params, 'label', 'Copia le lettere a sinistra')) }}"
			{{ array_get($params, 'required', true) ? 'required' : '' }}">
		<small class="text-danger">{{ $errors->first(array_get($params,'name', 'captcha')) }}</small>
	</div>
</div>
