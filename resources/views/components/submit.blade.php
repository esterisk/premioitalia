	<div class="col-sm-12 text-{{ array_get($params, 'align', 'right') }}">
	@if(array_get($params, 'reset', false))
		<button class="btn btn-default" type="reset">{{ __('Reimposta') }}</button>
	@endif
		<button class="btn btn-{{ array_get($params, 'class', 'primary') }}"
			type="{{ array_get($params, 'type', 'submit') }}"
			@if(array_get($params, 'name'))
			name="{{ array_get($params, 'name') }}"
			@endif
			@if(array_get($params, 'value'))
			value="{{ array_get($params, 'value') }}"
			@endif
			{{ array_get($params, 'disabled', false) ? 'disabled' : '' }}>{{ array_get($params, 'label') }}</button>
	</div>
