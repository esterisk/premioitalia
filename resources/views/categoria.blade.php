        {{ Form::open(['route' => $route, 'onsubmit' => "return verificaSegnalazione()"]) }}
			<div class="col-sm-12">
				<h2>{{ $categoria->nome }}</h2>

				@for ($i=1; $i<$max; $i++)
				<div class="segnalazione">
					@foreach (explode(',',$categoria->campi) as $campo)
						@include('campi.'.$campo, [ 'categoria' => $categoria->id, 'index' => $i ])
					@endforeach
				</div>
				@endfor
			<div class="col-sm-12">
				<ul class="definizioni">
					<li>{{ $categoria->descrizione }}</li>
					@foreach ($categoria->definizioni as $def)
					<li><strong>{{ $def->titolo }}</strong> {{ $def->testo }}</li>
					@endforeach
				</ul>
			</div>
         	{{ Form::submitGroup(['label' => 'Inserisci', 'reset' => false ]) }}
        	
		</div>
		{{ Form::close() }}
