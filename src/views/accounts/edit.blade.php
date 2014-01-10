@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.accounts_edit') }}
@endsection

@section('main')

	{{ Breadcrumbs::leave(array(
		URL::action('ObjectController@index')=>Lang::get('avalon::messages.objects'),
		Lang::get('avalon::messages.account_edit'),
		)) }}

	{{ Form::open(array('class'=>'form-horizontal', 'url'=>URL::action('AccountController@update'))) }}
	
	<div class="form-group">
		{{ Form::label('title', Lang::get('avalon::messages.account_title'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::text('title', $account->title, array('class'=>'required form-control', 'autofocus'=>'autofocus')) }}
	    </div>
	</div>
	
	<div class="form-group">
		{{ Form::label('title', Lang::get('avalon::messages.account_color'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::text('color', $account->color, array('class'=>'required form-control color')) }}
	    </div>
	</div>
	
	<div class="form-group">
		{{ Form::label('title', Lang::get('avalon::messages.account_image'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::text('image', $account->image, array('class'=>'required form-control')) }}
	    </div>
	</div>
		
	<div class="form-group">
	    <div class="col-sm-10 col-sm-offset-2">
			{{ Form::submit(Lang::get('avalon::messages.site_save'), array('class'=>'btn btn-primary')) }}
			{{ HTML::link(URL::action('ObjectController@index'), Lang::get('avalon::messages.site_cancel'), array('class'=>'btn btn-default')) }}
	    </div>
	</div>

	{{ Form::close() }}
	
@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.account_edit_help') }}</p>
@endsection