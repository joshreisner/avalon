@extends('avalon::login.template')

@section('title')
	{{ Lang::get('avalon::messages.site_welcome') }}
@endsection

@section('main')
	{{ Form::open(array('action'=>'LoginController@getIndex', 'class'=>'form-horizontal')) }}
		
		<div class="modal show">
		
			<div class="modal-dialog">

			    <div class="modal-content">
	
					<div class="modal-header">
						<h3 class="modal-title">{{ Lang::get('avalon::messages.site_welcome') }}</h3>
					</div>
				
					<div class="modal-body">
							
						<div class="form-group">
							<label class="col-md-3 control-label" for="email">{{ Lang::get('avalon::messages.users_firstname') }}</label>
					    	<div class="col-md-9">
					    		<input type="text" name="firstname" class="form-control required" autofocus="autofocus">
					    	</div>
						</div>
						
						<div class="form-group">
							<label class="col-md-3 control-label" for="email">{{ Lang::get('avalon::messages.users_lastname') }}</label>
					    	<div class="col-md-9">
					    		<input type="text" name="lastname" class="form-control required">
					    	</div>
						</div>
						
						<div class="form-group">
							<label class="col-md-3 control-label" for="email">{{ Lang::get('avalon::messages.users_email') }}</label>
					    	<div class="col-md-9">
					    		<input type="text" name="email" class="form-control required email">
					    	</div>
						</div>
						
						<div class="form-group">
							<label class="col-md-3 control-label" for="password">{{ Lang::get('avalon::messages.users_password') }}</label>
					    	<div class="col-md-9">
					    		<input type="password" name="password" class="form-control required">
					    	</div>
						</div>
								
				    </div>
				
				    <div class="modal-footer">
				    	<a href="/" class="btn btn-default">{{ Lang::get('avalon::messages.site_cancel') }}</a>
				    	<!--<a href="/login/password" class="btn">{{ Lang::get('avalon::messages.users_password_reset') }}</a>-->
				    	<input type="submit" class="btn btn-primary" value="{{ Lang::get('avalon::messages.site_login') }}">
				    </div>
				
				</div>

			</div>
		
		</div>
		
	{{ Form::close() }}
@endsection