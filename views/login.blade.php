@layout('avalon::template')

@section('content')

{{ Form::open('login', 'POST', array('class'=>'form-horizontal')); }}

<div class="modal">
	<div class="modal-header">
		<h3>Please log in</h3>
	</div>

	<div class="modal-body">

		<div class="control-group">
    	{{ Form::label('email', 'Email', array('class'=>'control-label')) }}
    	<div class="controls">
    	{{ Form::text('email', '', array('autofocus'=>true)) }}
    	</div>
		</div>
		
		<div class="control-group">
    	{{ Form::label('password', 'Password', array('class'=>'control-label')) }}
    	<div class="controls">
    	{{ Form::password('password') }}
    	</div>
		</div>
		
    </div>

    <div class="modal-footer">
    	{{ Form::submit('Go', array('class'=>'btn btn-primary')); }}
    </div>

</div>

{{ Form::close() }}

@endsection