@extends('avalon::template')

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
		<a class="btn" href="/login/objects/{{ $object->id }}/settings"><i class="icon-cog"></i> {{ Lang::get('avalon::messages.objects_edit', array('title'=>$object->title)) }}</a>
		<a class="btn" href="/login/objects/{{ $object->id }}/fields"><i class="icon-list"></i> {{ Lang::get('avalon::messages.instances_fields') }}</a>
		<a class="btn active" href="/login/objects/add"><i class="icon-plus"></i> {{ Lang::get('avalon::messages.instances_create', array('title'=>$object->title)) }}</a>
	</div>

	<form class="form-horizontal" method="post" action="/login/objects/{{ $object->id }}">
	
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
			<a class="btn" href="/login/objects/{{ $object->id }}">{{ Lang::get('avalon::messages.site_cancel') }}</a>
		</div>
		
	</form>
@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.objects_create_help') }}</p>
@endsection