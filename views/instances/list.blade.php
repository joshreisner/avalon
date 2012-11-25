@layout('avalon::template')

@section('breadcrumbs')
	<h1>
		<a href="/"><i class="icon-home"></i></a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		<a href="{{ URL::to_route('objects') }}">Objects</a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		{{ $title }}
	</h1>
@endsection

@section('buttons')
	<nav class="btn-group">
		@if ($user->role == 1)
		<a class="btn" href="{{ URL::to_route('objects_edit', $object->id) }}"><i class="icon-cog"></i> Object Settings</a>
		<a class="btn" href="{{ URL::to_route('fields', $object->id) }}"><i class="icon-list-alt"></i> Fields</a>
		@endif
		@if (count($object->fields))
		<a class="btn" href="{{ URL::to_route('instances_add', $object->id) }}"><i class="icon-pencil"></i> Add New</a>
		@endif
	</nav>
@endsection

@section('main')
	@if (count($instances))
		<table class="table table-condensed" data-reorder="{{ URL::to_route('instances_reorder', $object->id) }}">
			<thead>
				<tr>
					@if ($object->orderby == 'precedence')
					<th class="reorder"></th>
					@endif
					@foreach ($columns as $column)
					<th class="{{ $column->type }} {{ $column->field_name }}">{{ $column->title }}</th>
					@endforeach
					<th class="span2 date">Last Update</th>
				</tr>
			</thead>
			<tbody>
		@foreach ($instances as $instance)
			   	<tr data-id="{{ $instance->id }}" data-precedence="{{ $instance->precedence }}">
			   		@if ($object->orderby == 'precedence')
			   		<td class="reorder"><i class="icon-reorder"></i></td>
			   		@endif
			   		@foreach ($columns as $column)
			   		<?php $column_name = $column->field_name?>
			   		<td><a href="{{ URL::to_route('instances_edit', array($object->id, $instance->id)) }}">{{ $instance->$column_name }}</a></td>
			   		@endforeach
			   		<td class="date"><span class="user">Josh</span>{{ $instance->updated_at }}</td>
			   	</tr>
		@endforeach
			</tbody>
		</table>
	@else
		<div class="alert">
			No {{ strtolower($object->title) }} have been added yet.
		</div>
	@endif
@endsection

@section('side')
	@if (!empty($object->list_help))
	<div class="inner">
		{{ nl2br($object->list_help) }}
	</div>
	@endif
@endsection