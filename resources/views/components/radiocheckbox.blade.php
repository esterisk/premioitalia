<div class="form-group clearfix {{ $errors->has(array_get($params, 'name')) ? 'has-error' : '' }}">
	<label class="col-sm-2 control-label">{{ __(array_get($params, 'label')) }}</label>
	<div class="col-sm-10">
		@foreach(array_get($params, 'options') as $key => $label)
		@if(!array_get($params, 'inline', false))
		<div class="{{ $type }}">
		@endif
		<label{!! array_get($params, 'inline', false) ? ' class="'.$type.'-inline"' : '' !!}>
			<input type="{{ $type }}" 
				id="{{ array_get($params, 'name') }}-{{ $key }}" 
				name="{{ array_get($params, 'name') }}" 
				value="{{ $key }}"
				{{ array_get($params, 'required', false) ? 'required' : '' }}
				{!! $key == (old(array_get($params, 'name')) ?: array_get($params, 'value')) ? ' checked="checked"' : ''!!}>
				{{ $label }}
		</label>
		@if(!array_get($params, 'inline', false))
		</div>
		@endif
		@endforeach
		<small class="text-danger">{{ $errors->first(array_get($params,'name')) }}</small>
	</div>
</div>
