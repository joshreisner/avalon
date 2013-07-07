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
		@if (($object->order_by == 'precedence') && (count($instances) > 1))
	<table class="table table-condensed draggable" data-draggable-url="{{ URL::action('InstanceController@postReorder', $object->id) }}">
		@else
	<table class="table table-condensed">
		@endif
	
		<thead>
		<tr>
			@if (($object->order_by == 'precedence') && (count($instances) > 1))
			<th class="draggy"></th>
			@endif
			@foreach($fields as $field)
			<th>{{ $field->title }}</th>
			@endforeach
			<th class="right">Updated</th>
		</tr>
		</thead>
		@foreach ($instances as $instance)
		<tr id="{{ $instance->id }}" @if (!$instance->active) class="inactive"@endif>
			@if (($object->order_by == 'precedence') && (count($instances) > 1))
			<td class="draggy"><i class="icon-reorder"></i></td>
			@endif
			@foreach($fields as $field)
			<td><a href="{{ URL::action('InstanceController@edit', array($object->id, $instance->id)) }}">{{ $instance->{$field->name} }}</a></td>
			@endforeach
			<td class="right">{{ $instance->updated_at }}</td>
			<td class="active">
				<a href="{{ URL::action('InstanceController@getActivate', array($object->id, $instance->id)) }}">
				@if (!$instance->active)
					<i class="icon-check-empty"></i>
				@else
					<i class="icon-check"></i>
				@endif
				</a>
			</td>
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