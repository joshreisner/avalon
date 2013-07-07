@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.fields') }}
@endsection

@section('main')
	<h1 class="breadcrumbs">
		<a href="/"><i class="icon-home"></i></a>
		<i class="icon-chevron-right"></i>
		<a href="{{ URL::action('ObjectController@index') }}">{{ Lang::get('avalon::messages.objects') }}</a>
		<i class="icon-chevron-right"></i>
		<a href="{{ URL::action('ObjectController@show', $object->id) }}">{{ $object->title }}</a>
		<i class="icon-chevron-right"></i>
		{{ Lang::get('avalon::messages.fields') }}
	</h1>
	
	<div class="btn-group">
		<a class="btn" href="{{ URL::action('FieldController@create', $object->id) }}"><i class="icon-plus"></i> {{ Lang::get('avalon::messages.fields_create') }}</a>
	</div>

	<table class="table table-condensed draggable" data-draggable-url="{{ URL::action('FieldController@postReorder', $object->id) }}">
		<thead>
		<tr>
			<th class="draggy"></th>
			<th>Title</th>
			<th>Type</th>
			<th>Name</th>
			<th class="right">Updated</th>
		</tr>
		</thead>
		@foreach ($fields as $field)
		<tr id="{{ $field->id }}">
			<td class="draggy"><i class="icon-reorder"></i></td>
			<td><a href="{{ URL::action('FieldController@edit', array($object->id, $field->id)) }}">{{ $field->title }}</a></td>
			<td>{{ $field->type }}</td>
			<td>{{ $object->name }}.{{ $field->name }}</td>
			<td class="right">{{ $field->updated_at }}</td>
		</tr>
		@endforeach
	</table>
@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.fields_list_help', array('title'=>$object->title)) }}</p>
@endsection