@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.objects') }} &lt; {{ Lang::get('avalon::messages.objects_create') }}
@endsection

@section('main')
	<h1 class="breadcrumbs">
		<a href="/"><i class="icon-home"></i></a>
		<i class="icon-chevron-right"></i>
		<a href="{{ URL::action('ObjectController@index') }}">{{ Lang::get('avalon::messages.objects') }}</a>
		<i class="icon-chevron-right"></i>
		<a href="{{ URL::action('ObjectController@show', $object->id) }}">{{ $object->title }}</a>
		<i class="icon-chevron-right"></i>
		{{ Lang::get('avalon::messages.instances_create') }}
	</h1>
	
	{{ Form::open(array('action'=>array('InstanceController@store', $object->id), 'class'=>'form-horizontal')) }}
	
		@foreach ($fields as $field)
		<div class="control-group">
			<label class="control-label" for="email">{{ $field->title }}</label>
	    	<div class="controls">
	    		<input type="text" name="{{ $field->name }}" class="required title"@if ($field->precedence == 1) autofocus="autofocus"@endif>
	    	</div>
		</div>
		@endforeach
		
		<div class="form-actions">
			<button type="submit" class="btn btn-primary">{{ Lang::get('avalon::messages.site_save') }}</button>
			<a class="btn" href="{{ URL::action('ObjectController@show', $object->id) }}">{{ Lang::get('avalon::messages.site_cancel') }}</a>
		</div>
		
	{{ Form::close() }}
@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.objects_create_help') }}</p>
@endsection