@extends('avalon::login.template')

@section('title')
	@lang('avalon::messages.site_welcome')
@endsection

@section('main')
	{{ Form::open(['action'=>'LoginController@postIndex', 'class'=>'form-horizontal']) }}
		
	<div class="modal show">
		<div class="modal-dialog">
		    <div class="modal-content">
				<div class="modal-header">
					<h2 class="modal-title">{{ trans('avalon::messages.site_welcome') }}</h2>
				</div>
				<div class="modal-body">
					@include('avalon::login.notifications')
					<div class="form-group">
						<label class="col-md-3 control-label" for="email">{{ trans('avalon::messages.users_email') }}</label>
				    	<div class="col-md-9">
				    		<input type="text" name="email" class="form-control required email" autofocus="autofocus">
				    	</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label" for="password">{{ trans('avalon::messages.users_password') }}</label>
				    	<div class="col-md-9">
				    		<input type="password" name="password" class="form-control required">
				    	</div>
					</div>
			    </div>
			    <div class="modal-footer">
			    	<a href="{{ URL::action('LoginController@getReset') }}" class="btn btn-default">{{ trans('avalon::messages.users_password_reset') }}</a>
			    	<input type="submit" class="btn btn-primary" value="{{ trans('avalon::messages.site_login') }}">
			    </div>
			</div>
		</div>
	</div>
		
	{{ Form::close() }}
@endsection