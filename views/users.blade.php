@layout('avalon::page')

@section('breadcrumbs')
	<h1>
		<a href="/"><i class="icon-home"></i></a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		<a href="/login/objects">Objects</a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		Users		
	</h1>
@endsection

@section('buttons')
	<div class="btn-group">
		<a class="btn" href="{{ URL::to_route('users_add') }}"><i class="icon-pencil"></i> Add New User</a>
	</div>
@endsection

@section('main')

	@if (count($users) > 0)
		<table class="table">
			<thead>
				<tr>
					<th>Name</th>
					<th>Role</th>
					<th>Last Login</th>
				</tr>
			</thead>
			<tbody>
		@foreach ($users as $user)
			   	<tr>
			   		<td><a href="/login/users/{{ $user->id }}">{{ $user->lastname }}, {{ $user->firstname }}</a></td>
			   		<td>{{ $user->role }}</td>
			   		<td>{{ $user->last_login }}</td>
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
