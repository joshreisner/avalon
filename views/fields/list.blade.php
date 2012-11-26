@layout('avalon::template')

@section('breadcrumbs')
	<h1>
		<a href="/"><i class="icon-home"></i></a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		<a href="{{ URL::to_route('objects') }}">Objects</a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		<a href="{{ URL::to_route('instances', $object->id) }}">{{ $object->title }}</a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		{{ $title }}		
	</h1>
@endsection

@section('buttons')
	<nav class="btn-group">
		<a class="btn" href="{{ URL::to_route('fields_add', $object->id) }}"><i class="icon-pencil"></i> Add New</a>
	</nav>
@endsection

@section('main')
	@if (count($fields))
		<table class="table table-condensed" data-reorder="{{ URL::to_route('fields_reorder', $object->id) }}">
			<thead>
				<tr>
					<th class="reorder"></th>
					<th>Object</th>
					<th>Type</th>
					<th>Table &amp; Column</th>
					<th class="span2 date">Last Update</th>
					<th class="delete"></th>
				</tr>
			</thead>
			<tbody>
		@foreach ($fields as $field)
			   	<tr data-id="{{ $field->id }}" data-precedence="{{ $field->precedence }}">
			   		<td class="reorder"><i class="icon-reorder"></i></td>
			   		<td><a href="{{ $field->link }}">{{ $field->title }}</a></td>
			   		<td>{{ $field->type }}</td>
			   		<td>{{ $object->table_name }}.{{ $field->field_name }}</td>
			   		<td class="date"><span class="user">Josh</span>{{ $field->updated_at }}</td>
			   		<td class="delete"><a href="{{ $field->link }}">&times;</a></td>
			   	</tr>
		@endforeach
			</tbody>
		</table>
	@else
		<div class="alert">No fields have been added yet.</div>
	@endif
@endsection

@section('side')
	<div class="inner">
		<p>These are the fields that belong to the {{ $object->title }} object.</p>
	</div>
@endsection