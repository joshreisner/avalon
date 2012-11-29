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

@if ($user->role < 3)

@section('buttons')
	<nav class="btn-group">
		<a class="btn" href="{{ URL::to_route('users_add') }}"><i class="icon-pencil"></i> Add User</a>
	</nav>
@endsection

@endif

@section('main')

	@if (count($users) > 0)
		<table class="table table-condensed">
			<thead>
				<tr>
					<th class="string">Name</th>
					<th class="string">Role</th>
					<th class="date updated">Last Login</th>
					@if ($user->role < 3)
					<th class="delete"></th>
					@endif
				</tr>
			</thead>
			<tbody>
		@foreach ($users as $u)
			   	<tr>
			   		<td class="string"><a href="{{ $u->link }}">{{ $u->lastname }}, {{ $u->firstname }}</a></td>
			   		<td class="string">{{ $u->role }}</td>
			   		<td class="date updated">{{ $u->last_login }}</td>
					@if ($user->role < 3)
					<td class="delete">@if ($u->id != $user->id)<a href="{{ $u->link }}">&times;</a>@endif</td>
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
