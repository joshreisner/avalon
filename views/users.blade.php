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
		<a class="btn" href="{{ URL::to_route('objects_new') }}"><i class="icon-pencil"></i> Add New Object</a>
	</div>
@endsection

@section('main')

	@if (count($users) > 0)
		<table class="table">
			<thead>
				<tr>
					<th>Name</th>
					<th>Foo</th>
					<th>Foo</th>
					<th>Foo</th>
				</tr>
			</thead>
			<tbody>
		@foreach ($users as $user)
			   	<tr>
			   		<td>{{ $user->lastname }}, {{ $user->firstname }}</td>
			   		<td>Ok</td>
			   		<td>Ok!</td>
			   		<td>duDe</td>
			   	</tr>
		@endforeach
			</tbody>
		</table>
	@else
		<div class="alert">No users have been entered yet, which means I'm not sure how you're reading this message.</div>
	@endif

@endsection

@section('side')
	<div class="inner">
		These users have access to the CMS.
	</div>
@endsection
