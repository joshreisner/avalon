@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.fields_edit') }}
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
		{{ Lang::get('avalon::messages.fields_edit') }}
	</h1>
	
	{{ Form::open(array('action'=>array('FieldController@store', $object->id), 'class'=>'form-horizontal')) }}

		<div class="control-group">
			<label class="control-label" for="title">{{ Lang::get('avalon::messages.fields_title') }}</label>
	    	<div class="controls">
	    		<input type="text" id="title" name="title" class="required" autofocus="autofocus" value="{{ $field->title }}">
	    	</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="name">{{ Lang::get('avalon::messages.fields_title') }}</label>
	    	<div class="controls">
	    		<input type="text" id="name" name="name" class="required" value="{{ $field->name }}">
	    	</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="type">{{ Lang::get('avalon::messages.fields_type') }}</label>
	    	<div class="controls">
	    		{{ Form::select('type', $types, $field->type, array('disabled'=>'disabled')) }}
	    	</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="visibility">{{ Lang::get('avalon::messages.fields_visibility') }}</label>
	    	<div class="controls">
	    		{{ Form::select('visibility', $visibility) }}
	    	</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="required">{{ Lang::get('avalon::messages.fields_required') }}</label>
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
	<p>{{ Lang::get('avalon::messages.fields_edit_help') }}</p>
	{{ Form::open(array('method'=>'delete', 'action'=>array('FieldController@destroy', $object->id, $field->id))) }}
	<button type="submit" class="btn btn-mini">{{ Lang::get('avalon::messages.fields_destroy') }}</button>
	{{ Form::close() }}	
@endsection