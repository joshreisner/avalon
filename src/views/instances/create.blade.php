@extends('avalon::template');

@section('title')
	{{ Lang::get('avalon::messages.objects') }} &lt; {{ Lang::get('avalon::messages.objects_create') }}
@endsection

@section('main')
	<h1 class="breadcrumbs">
		<a href="/"><i class="icon-home"></i></a>
		<i class="icon-chevron-right"></i>
		<a href="/login/objects">{{ Lang::get('avalon::messages.objects') }}</a>
		<i class="icon-chevron-right"></i>
		<a href="/login/objects/{{ $object->id }}">{{ $object->title }}</a>
		<i class="icon-chevron-right"></i>
		{{ Lang::get('avalon::messages.instances_create') }}
	</h1>
	
	<div class="btn-group">
		<a class="btn" href="/login/objects/{{ $object->id }}/settings"><i class="icon-cog"></i> {{ Lang::get('avalon::messages.objects_settings', array('title'=>$object->title)) }}</a>
		<a class="btn active" href="/login/objects/add"><i class="icon-plus"></i> {{ Lang::get('avalon::messages.instances_create', array('title'=>$object->title)) }}</a>
	</div>

	<form class="form-horizontal" method="post" action="/login/objects/create">
	
		@foreach ($fields as $field)
		<div class="control-group">
			<label class="control-label" for="email">{{ $field->title }}</label>
	    	<div class="controls">
	    		<input type="text" name="{{ $field->field_name }}" class="required title"@if ($field->precedence == 1) autofocus="autofocus"@endif>
	    	</div>
		</div>
		@endforeach
		
		<div class="control-group">
	    	<div class="controls">
	    		<input type="submit" class="btn" value="{{ Lang::get('avalon::messages.objects_create') }}">
	    	</div>
		</div>
	</form>
@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.objects_create_help') }}</p>
@endsection