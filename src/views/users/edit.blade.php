@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.users_edit') }}
@endsection

@section('main')

	{{ Breadcrumbs::leave(array(
		URL::action('ObjectController@index')=>Lang::get('avalon::messages.objects'),
		URL::action('UserController@index')=>Lang::get('avalon::messages.users'),
		Lang::get('avalon::messages.users_edit'),
		)) }}

	{{ Form::open(array('class'=>'form-horizontal', 'url'=>URL::action('UserController@update', $user->id), 'method'=>'put')) }}

	<div class="form-group">
		{{ Form::label('firstname', Lang::get('avalon::messages.users_firstname'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::text('firstname', $user->firstname, array('class'=>'form-control required')) }}
	    </div>
	</div>
	
	<div class="form-group">
		{{ Form::label('lastname', Lang::get('avalon::messages.users_lastname'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::text('lastname', $user->lastname, array('class'=>'form-control required')) }}
	    </div>
	</div>

	<div class="form-group">
		{{ Form::label('email', Lang::get('avalon::messages.users_email'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::email('email', $user->email, array('class'=>'form-control required')) }}
	    </div>
	</div>
	
	<div class="form-group">
		{{ Form::label('role', Lang::get('avalon::messages.users_role'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::select('role', $roles, $user->role, array('class'=>'form-control')) }}
	    </div>
	</div>

	<div class="form-group">
	    <div class="col-sm-10 col-sm-offset-2">
			{{ Form::submit(Lang::get('avalon::messages.site_save'), array('class'=>'btn btn-primary')) }}
			{{ HTML::link(URL::action('UserController@index'), Lang::get('avalon::messages.site_cancel'), array('class'=>'btn btn-default')) }}
	    </div>
	</div>
	
	{{ Form::close() }}
		
@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.users_edit_help') }}</p>
@endsection