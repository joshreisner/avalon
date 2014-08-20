@extends('avalon::login.template')

@section('title')
	{{ Lang::get('avalon::messages.users_password_reset') }}
@endsection

@section('main')
	{{ Form::open(array('action'=>'LoginController@postReset', 'class'=>'form-horizontal')) }}
		
	<div class="modal show">
		<div class="modal-dialog">
		    <div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title">{{ Lang::get('avalon::messages.users_password_reset') }}</h3>
				</div>
				<div class="modal-body">
					@include('avalon::login.notifications')
					<div class="form-group">
						<label class="col-md-3 control-label" for="email">{{ Lang::get('avalon::messages.users_email') }}</label>
				    	<div class="col-md-9">
				    		<input type="text" name="email" class="form-control required email" autofocus="autofocus">
				    	</div>
					</div>
			    </div>
			    <div class="modal-footer">
			    	<a href="{{ URL::action('LoginController@getIndex') }}" class="btn btn-default">{{ Lang::get('avalon::messages.site_cancel') }}</a>
			    	<input type="submit" class="btn btn-primary" value="{{ Lang::get('avalon::messages.users_password_reset') }}">
			    </div>
			</div>
		</div>
	</div>
		
	{{ Form::close() }}
@endsection