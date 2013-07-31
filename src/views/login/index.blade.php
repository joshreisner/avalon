@extends('avalon::login.template')

@section('title')
	{{ Lang::get('avalon::messages.site_welcome') }}
@endsection

@section('main')
	{{ Form::open(array('action'=>'LoginController@getIndex', 'class'=>'form-horizontal')) }}
		
		<div class="modal">
		
			<div class="modal-header">
				<h3>{{ Lang::get('avalon::messages.site_welcome') }}</h3>
			</div>
		
			<div class="modal-body">
						
				<div class="control-group">
					<label class="control-label" for="email">{{ Lang::get('avalon::messages.users_email') }}</label>
			    	<div class="controls">
			    		<input type="text" name="email" class="required email" autofocus="autofocus">
			    	</div>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="password">{{ Lang::get('avalon::messages.users_password') }}</label>
			    	<div class="controls">
			    		<input type="password" name="password" class="required">
			    	</div>
				</div>
						
		    </div>
		
		    <div class="modal-footer">
		    	<a href="/" class="btn">{{ Lang::get('avalon::messages.site_cancel') }}</a>
		    	<!--<a href="/login/password" class="btn">{{ Lang::get('avalon::messages.users_password_reset') }}</a>-->
		    	<input type="submit" class="btn btn-primary" value="{{ Lang::get('avalon::messages.site_login') }}">
		    </div>
		
		</div>
		
	{{ Form::close() }}
@endsection