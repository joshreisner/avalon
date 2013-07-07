@extends('avalon::template')

@section('title')
	{{ $object->title }}
@endsection

@section('main')

	{{ Breadcrumbs::leave(array(
		URL::action('ObjectController@index')=>Lang::get('avalon::messages.objects'),
		$object->title,
		)) }}

	<div class="btn-group">
		<a class="btn" href="{{ URL::action('ObjectController@edit', $object->id) }}"><i class="icon-cog"></i> {{ Lang::get('avalon::messages.objects_edit', array('title'=>$object->title)) }}</a>
		<a class="btn" href="{{ URL::action('FieldController@index', $object->id) }}"><i class="icon-list"></i> {{ Lang::get('avalon::messages.fields') }}</a>
		<a class="btn" href="{{ URL::action('InstanceController@create', $object->id) }}"><i class="icon-plus"></i> {{ Lang::get('avalon::messages.instances_create') }}</a>
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
			<td><a href="{{ URL::action('InstanceController@edit', array($object->id, $instance->id)) }}">{{ $instance->{$field->name} }}</a></td>
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