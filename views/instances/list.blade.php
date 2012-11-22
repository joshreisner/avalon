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
		<a class="btn" href="{{ URL::to_route('objects_edit', $object->id) }}"><i class="icon-cog"></i> Object Settings</a>
		<a class="btn" href="{{ URL::to_route('fields', $object->id) }}"><i class="icon-cog"></i> Fields</a>
		<!--<a class="btn" href="{{ URL::to_route('instances_add', $object->id) }}"><i class="icon-pencil"></i> Add New</a>-->
	</nav>
@endsection

@section('main')
	<div class="alert">
		No {{ strtolower($object->title) }} have been added yet.
	</div>
@endsection