@extends('avalon::template')

@section('title')
	@lang('avalon::messages.users_edit')
@endsection

@section('main')

	{{ Breadcrumbs::leave([
		URL::action('ObjectController@index')=>trans('avalon::messages.objects'),
		URL::action('UserController@index')=>trans('avalon::messages.users'),
		trans('avalon::messages.users_edit'),
		]) }}

	{{ Form::open(['class'=>'form-horizontal', 'url'=>URL::action('UserController@update', $user->id), 'method'=>'put']) }}

	<div class="form-group">
		{{ Form::label('name', trans('avalon::messages.users_name'), ['class'=>'control-label col-sm-2']) }}
	    <div class="col-sm-10">
			{{ Form::text('name', $user->name, ['class'=>'form-control required']) }}
	    </div>
	</div>
	
	<div class="form-group">
		{{ Form::label('email', trans('avalon::messages.users_email'), ['class'=>'control-label col-sm-2']) }}
	    <div class="col-sm-10">
			{{ Form::email('email', $user->email, ['class'=>'form-control required']) }}
	    </div>
	</div>
	
	<div class="form-group">
		{{ Form::label('role', trans('avalon::messages.users_role'), ['class'=>'control-label col-sm-2']) }}
	    <div class="col-sm-10">
	    	@foreach ($roles as $role_id=>$role)
			<div class="radio">
				<label>
					{{ Form::radio('role', $role_id, $role_id == $user->role) }}
					<strong>{{ $role }}</strong> &middot; {{ trans('avalon::messages.users_role_' . Str::slug($role)) }}
				</label>
			</div>
			@endforeach
		</div>
	</div>

	<div class="form-group">
	    <div class="col-sm-10 col-sm-offset-2">
			{{ Form::submit(trans('avalon::messages.site_save'), ['class'=>'btn btn-primary']) }}
			{{ HTML::link(URL::action('UserController@index'), trans('avalon::messages.site_cancel'), ['class'=>'btn btn-default']) }}
	    </div>
	</div>
	
	{{ Form::close() }}
		
@endsection

@section('side')
	<p>@lang('avalon::messages.users_edit_help')</p>

	<p><a href="{{ URL::action('UserController@resendWelcome', $user->id) }}" class="btn btn-default btn-xs">@lang('avalon::messages.users_welcome_resend')</a></p>

	@if (!$user->last_login)
	{{ Form::open(['method'=>'delete', 'action'=>['UserController@destroy', $user->id]]) }}
	<button type="submit" class="btn btn-default btn-xs">{{ trans('avalon::messages.users_destroy') }}</button>
	{{ Form::close() }}
	@endif
@endsection