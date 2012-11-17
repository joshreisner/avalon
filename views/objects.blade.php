@layout('avalon::page')

@section('breadcrumbs')
	<h1>
		<a href="/"><i class="icon-home"></i></a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		Objects
	</h1>
@endsection

@section('buttons')
	<div class="btn-group">
		<a class="btn" href="{{ URL::to_route('settings') }}"><i class="icon-cogs"></i> Site Settings</a>
		<a class="btn" href="{{ URL::to_route('users') }}"><i class="icon-group"></i> Users</a>
		<a class="btn" href="{{ URL::to_route('objects_add') }}"><i class="icon-pencil"></i> Add New Object</a>
	</div>
@endsection

@section('main')

	@if (count($objects) > 0)
		<table class="table">
			<thead>
				<tr>
					<th>Foo</th>
					<th>Foo</th>
					<th>Foo</th>
					<th>Foo</th>
				</tr>
			</thead>
			<tbody>
		@foreach ($objects as $object)
			   	<tr>
			   		<td>This is an object</td>
			   		<td>Ok</td>
			   		<td>Ok!</td>
			   		<td>duDe</td>
			   	</tr>
		@endforeach
			</tbody>
		</table>
	@else
		<div class="alert">No objects have been entered yet.</div>
	@endif

@endsection

@section('side')
	<div class="inner">
		<p>This is the main directory of website ‘objects.’ Those that are linked you have permission to edit.</p>
		<p>You're logged in as {{ $user->firstname }}.<br>Click {{ HTML::link_to_route('logout', 'here') }} to log out.</p>
	</div>
@endsection
