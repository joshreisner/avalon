@layout('avalon::template')

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
	<nav class="btn-group">
		<a class="btn" href="{{ URL::to_route('users_add') }}"><i class="icon-pencil"></i> Add New User</a>
	</nav>
@endsection

@section('main')

	@if (count($users) > 0)
		<table class="table table-condensed">
			<thead>
				<tr>
					<th class="string">Name</th>
					<th class="string">Role</th>
					<th class="date">Last Login</th>
					@if ($role < 3)
					<th class="delete"></th>
					@endif
				</tr>
			</thead>
			<tbody>
		@foreach ($users as $u)
			   	<tr>
			   		<td class="string"><a href="/login/users/{{ $u->id }}">{{ $u->lastname }}, {{ $u->firstname }}</a></td>
			   		<td class="string">{{ $u->role }}</td>
			   		<td class="date">{{ $u->last_login }}</td>
					@if ($role < 3)
					<td class="delete">@if ($u->id != $user->id)<a href="/login/users/{{ $u->id }}">&times;</a>@endif</td>
					@endif
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
