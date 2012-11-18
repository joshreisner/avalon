@layout('avalon::template')

@section('breadcrumbs')
	<h1>
		<a href="/"><i class="icon-home"></i></a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		<a href="{{ URL::to_route('objects') }}">Objects</a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		<a href="{{ URL::to_route('instances', $object->id) }}">{{ $object->title }}</a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		Fields		
	</h1>
@endsection

@section('buttons')
	<nav class="btn-group">
		<a class="btn" href="{{ URL::to_route('fields_add', $object->id) }}"><i class="icon-pencil"></i> Add New</a>
	</nav>
@endsection

@section('main')
	<div class="alert">
		No fields have been added yet.
	</div>
@endsection

@section('side')
	<div class="inner">
		<p>These are the fields that belong to the {{ $object->title }} object.</p>
	</div>
@endsection