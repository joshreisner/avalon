@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.fields_create') }}
@endsection

@section('main')

	<h1 class="breadcrumbs">
		<a href="/"><i class="icon-home"></i></a>
		<i class="icon-chevron-right"></i>
		<a href="{{ URL::action('ObjectController@index') }}">{{ Lang::get('avalon::messages.objects') }}</a>
		<i class="icon-chevron-right"></i>
		<a href="{{ URL::action('ObjectController@show', $object->id) }}">{{ $object->title }}</a>
		<i class="icon-chevron-right"></i>
		<a href="{{ URL::action('FieldController@index', $object->id) }}">{{ Lang::get('avalon::messages.fields') }}</a>
		<i class="icon-chevron-right"></i>
		{{ Lang::get('avalon::messages.fields_create') }}
	</h1>
	
	{{ Form::open(array('action'=>array('FieldController@store', $object->id), 'class'=>'form-horizontal')) }}

		<div class="control-group">
			<label class="control-label" for="email">{{ Lang::get('avalon::messages.fields_title') }}</label>
	    	<div class="controls">
	    		<input type="text" name="title" class="required" autofocus="autofocus">
	    	</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="email">{{ Lang::get('avalon::messages.fields_type') }}</label>
	    	<div class="controls">
	    		{{ Form::select('type', $types) }}
	    	</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="email">{{ Lang::get('avalon::messages.fields_visibility') }}</label>
	    	<div class="controls">
	    		{{ Form::select('visibility', $visibility) }}
	    	</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="email">{{ Lang::get('avalon::messages.fields_required') }}</label>
	    	<div class="controls">
				<label class="checkbox">
					<input type="checkbox" name="required">
				</label>
	    	</div>
		</div>

		<div class="form-actions">
			<button type="submit" class="btn btn-primary">{{ Lang::get('avalon::messages.site_save') }}</button>
			<a class="btn" href="{{ URL::action('FieldController@index', $object->id) }}">{{ Lang::get('avalon::messages.site_cancel') }}</a>
		</div>
		
	{{ Form::close() }}
	
@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.objects_create_help') }}</p>
@endsection