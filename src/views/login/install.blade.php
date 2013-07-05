@extends('avalon::login.template')

@section('title')
	{{ Lang::get('avalon::messages.site_welcome') }}
@endsection

@section('main')
	<form method="post" action="/login" class="form-horizontal">
		
		<div class="modal">
		
			<div class="modal-header">
				<h3>{{ Lang::get('avalon::messages.site_welcome') }}</h3>
			</div>
		
			<div class="modal-body">
						
				<div class="control-group">
					<label class="control-label" for="email">{{ Lang::get('avalon::messages.users_firstname') }}</label>
			    	<div class="controls">
			    		<input type="text" name="firstname" class="required" autofocus="autofocus">
			    	</div>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="email">{{ Lang::get('avalon::messages.users_lastname') }}</label>
			    	<div class="controls">
			    		<input type="text" name="lastname" class="required">
			    	</div>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="email">{{ Lang::get('avalon::messages.users_email') }}</label>
			    	<div class="controls">
			    		<input type="text" name="email" class="required email">
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
		    	<input type="submit" class="btn btn-primary" value="{{ Lang::get('avalon::messages.site_install') }}">
		    </div>
		
		</div>
		
	</form>
@endsection