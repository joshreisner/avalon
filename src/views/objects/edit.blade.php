@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.objects_edit', array('title'=>$object->title)) }}
@endsection

@section('main')
	<h1 class="breadcrumbs">
		<a href="/"><i class="icon-home"></i></a>
		<i class="icon-chevron-right"></i>
		<a href="/login/objects">{{ Lang::get('avalon::messages.objects') }}</a>
		<i class="icon-chevron-right"></i>
		{{ Lang::get('avalon::messages.objects_edit', array('title'=>$object->title)) }}
	</h1>
	
	<div class="btn-group">
		<a class="btn active" href="/login/objects/{{ $object->id }}/edit"><i class="icon-cog"></i> {{ Lang::get('avalon::messages.objects_edit', array('title'=>$object->title)) }}</a>
		<a class="btn" href="/login/objects/{{ $object->id }}/fields"><i class="icon-list"></i> {{ Lang::get('avalon::messages.instances_fields') }}</a>
		<a class="btn" href="/login/objects/{{ $object->id }}/create"><i class="icon-plus"></i> {{ Lang::get('avalon::messages.instances_create') }}</a>
	</div>

	{{ Form::open(array('action'=>array('ObjectController@update', $object->id), 'class'=>'form-horizontal', 'method'=>'put')) }}

		<div class="control-group">
			<label class="control-label" for="title">{{ Lang::get('avalon::messages.objects_title') }}</label>
	    	<div class="controls">
	    		<input type="text" id="title" name="title" class="required" value="{{ $object->title }}" autofocus="autofocus">
	    	</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="name">{{ Lang::get('avalon::messages.objects_name') }}</label>
	    	<div class="controls">
	    		<input type="text" id="name" name="name" class="required" value="{{ $object->name }}">
	    	</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="list_help">{{ Lang::get('avalon::messages.objects_list_help') }}</label>
	    	<div class="controls">
	    		<textarea id="list_help" name="list_help">{{ $object->list_help }}</textarea>
	    	</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="form_help">{{ Lang::get('avalon::messages.objects_form_help') }}</label>
	    	<div class="controls">
	    		<textarea id="form_help" name="form_help">{{ $object->form_help }}</textarea>
			</div>
		</div>

		<div class="form-actions">
			<button type="submit" class="btn btn-primary">{{ Lang::get('avalon::messages.site_save') }}</button>
			<a class="btn" href="/login/objects/{{ $object->id }}">{{ Lang::get('avalon::messages.site_cancel') }}</a>
		</div>
		
	{{ Form::close() }}
	
@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.objects_create_help') }}</p>
@endsection