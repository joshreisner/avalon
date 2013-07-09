@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.fields') }}
@endsection

@section('main')

	{{ Breadcrumbs::leave(array(
		URL::action('ObjectController@index')=>Lang::get('avalon::messages.objects'),
		URL::action('ObjectController@show', $object->id)=>$object->title,
		Lang::get('avalon::messages.fields'),
		)) }}

	<div class="btn-group">
		<a class="btn" href="{{ URL::action('FieldController@create', $object->id) }}"><i class="icon-plus"></i> {{ Lang::get('avalon::messages.fields_create') }}</a>
	</div>

	<table class="table table-condensed draggable" data-draggable-url="{{ URL::action('FieldController@postReorder', $object->id) }}">
		<thead>
		<tr>
			<th class="draggy"></th>
			<th>{{ Lang::get('avalon::messages.fields_title') }}</th>
			<th>{{ Lang::get('avalon::messages.fields_type') }}</th>
			<th>{{ Lang::get('avalon::messages.fields_name') }}</th>
			<th class="date">{{ Lang::get('avalon::messages.site_updated') }}</th>
		</tr>
		</thead>
		@foreach ($fields as $field)
		<tr id="{{ $field->id }}">
			<td class="draggy"><i class="icon-reorder"></i></td>
			<td><a href="{{ URL::action('FieldController@edit', array($object->id, $field->id)) }}">{{ $field->title }}</a></td>
			<td>{{ $types[$field->type] }}</td>
			<td>{{ $object->name }}.{{ $field->name }}</td>
			<td class="date">{{ Dates::relative($field->updated_at) }}</td>
		</tr>
		@endforeach
	</table>
@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.fields_list_help', array('title'=>$object->title)) }}</p>
@endsection