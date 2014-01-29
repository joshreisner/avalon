@extends('avalon::login.template')

@section('title')
	{{ $account->title }}
@endsection

@section('main')
	{{ Form::open(array('action'=>'LoginController@postIndex', 'class'=>'form-horizontal')) }}
		
	<div class="modal show">
		<div class="modal-dialog">
		    <div class="modal-content">
				<div class="modal-header">
					<h2 class="modal-title">{{ $account->title }}</h2>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label class="col-md-3 control-label" for="email">{{ Lang::get('avalon::messages.users_email') }}</label>
				    	<div class="col-md-9">
				    		<input type="text" name="email" class="form-control required email" autofocus="autofocus">
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
			    	<a href="{{ URL::action('LoginController@getReset') }}" class="btn btn-default">{{ Lang::get('avalon::messages.users_password_reset') }}</a>
			    	<input type="submit" class="btn btn-primary" value="{{ Lang::get('avalon::messages.site_login') }}">
			    </div>
			</div>
		</div>
	</div>
		
	{{ Form::close() }}
@endsection