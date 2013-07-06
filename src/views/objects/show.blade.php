@extends('avalon::template')

@section('title')
	{{ $object->title }}
@endsection

@section('main')
	<h1 class="breadcrumbs">
		<a href="/"><i class="icon-home"></i></a>
		<i class="icon-chevron-right"></i>
		<a href="/login/objects">{{ Lang::get('avalon::messages.objects') }}</a>
		<i class="icon-chevron-right"></i>
		{{ $object->title }}
	</h1>
	
	<div class="btn-group">
		<a class="btn" href="/login/objects/{{ $object->id }}/edit"><i class="icon-cog"></i> {{ Lang::get('avalon::messages.objects_edit', array('title'=>$object->title)) }}</a>
		<a class="btn" href="/login/objects/{{ $object->id }}/fields"><i class="icon-list"></i> {{ Lang::get('avalon::messages.instances_fields') }}</a>
		<a class="btn" href="/login/objects/{{ $object->id }}/create"><i class="icon-plus"></i> {{ Lang::get('avalon::messages.instances_create') }}</a>
	</div>

	@if (count($instances))
	<table class="table table-condensed">
		<thead>
		<tr>
			@foreach($fields as $field)
			<th>{{ $field->title }}</th>
			@endforeach
			<th class="right">Updated</th>
		</tr>
		</thead>
		@foreach ($instances as $instance)
		<tr>
			@foreach($fields as $field)
			<?php $field_name = $field->name //make this less of a hack ?>
			<td>{{ $instance->$field_name }}</td>
			@endforeach
			<td class="right">{{ $instance->updated_at }}</td>
		</tr>
		@endforeach
	</table>
	@else
	<div class="alert">
		{{ Lang::get('avalon::messages.instances_empty', array('title'=>$object->title)) }}
	</div>
	@endif
@endsection

@section('side')
	<p>{{ $object->list_help }}</p>
@endsection