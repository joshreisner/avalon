@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.objects_create') }}
@endsection

@section('main')

	<h1 class="breadcrumbs">
		<a href="/"><i class="icon-home"></i></a>
		<i class="icon-chevron-right"></i>
		<a href="{{ URL::action('ObjectController@index') }}">{{ Lang::get('avalon::messages.objects') }}</a>
		<i class="icon-chevron-right"></i>
		{{ Lang::get('avalon::messages.objects_create') }}
	</h1>
	
	{{ Form::open(array('action'=>'ObjectController@store', 'class'=>'form-horizontal')) }}

		<div class="control-group">
			<label class="control-label" for="email">{{ Lang::get('avalon::messages.objects_title') }}</label>
	    	<div class="controls">
	    		<input type="text" name="title" class="required" autofocus="autofocus">
				<span class="help-inline">{{ Lang::get('avalon::messages.objects_title_help') }}</span>

	    	</div>
		</div>

		<div class="form-actions">
			<button type="submit" class="btn btn-primary">{{ Lang::get('avalon::messages.site_save') }}</button>
			<a class="btn" href="{{ URL::action('ObjectController@index') }}">{{ Lang::get('avalon::messages.site_cancel') }}</a>
		</div>
		
	{{ Form::close() }}
	
@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.objects_create_help') }}</p>
@endsection