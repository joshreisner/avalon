@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.users_create') }}
@endsection

@section('main')

	{{ Breadcrumbs::leave(array(
		URL::action('ObjectController@index')=>Lang::get('avalon::messages.objects'),
		URL::action('UserController@index')=>Lang::get('avalon::messages.users'),
		Lang::get('avalon::messages.users_create'),
		)) }}

	{{ Form::open(array('class'=>'form-horizontal', 'url'=>URL::action('UserController@store'))) }}
	
	<div class="form-group">
		{{ Form::label('name', Lang::get('avalon::messages.users_name'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::text('name', false, array('class'=>'form-control required')) }}
	    </div>
	</div>

	<div class="form-group">
		{{ Form::label('email', Lang::get('avalon::messages.users_email'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::email('email', false, array('class'=>'form-control required')) }}
	    </div>
	</div>
	
	<div class="form-group">
		{{ Form::label('role', Lang::get('avalon::messages.users_role'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
	    	@foreach ($roles as $role_id=>$role)
			<div class="radio">
				<label>
					{{ Form::radio('role', $role_id, $role_id == 3) }}
					<strong>{{ $role }}</strong> &middot; {{ Lang::get('avalon::messages.users_role_' . Str::slug($role)) }}
				</label>
			</div>
			@endforeach
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
	<p>{{ Lang::get('avalon::messages.users_form_help') }}</p>
@endsection