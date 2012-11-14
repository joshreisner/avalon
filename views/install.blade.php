@layout('avalon::template')

@section('content')

{{ Form::open('login', 'POST', array('class'=>'form-horizontal')); }}

<div class="modal">
	<div class="modal-header">
		<h3>Hello there!</h3>
	</div>

	<div class="modal-body">

		<div class="control-group">
    	{{ Form::label('firstname', 'First Name', array('class'=>'control-label')) }}
    	<div class="controls">
    	{{ Form::text('firstname', '', array('autofocus'=>true)) }}
    	</div>
		</div>
		
		<div class="control-group">
    	{{ Form::label('lastname', 'Last Name', array('class'=>'control-label')) }}
    	<div class="controls">
    	{{ Form::text('lastname', '') }}
    	</div>
		</div>
		
		<div class="control-group">
    	{{ Form::label('email', 'Email', array('class'=>'control-label')) }}
    	<div class="controls">
    	{{ Form::text('email') }}
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
    	{{ Form::submit('Create Your Account', array('class'=>'btn btn-primary')); }}
    </div>

</div>

{{ Form::close() }}

@endsection