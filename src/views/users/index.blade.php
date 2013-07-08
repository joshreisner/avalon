@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.users') }}
@endsection

@section('main')

	{{ Breadcrumbs::leave(array(
		URL::action('ObjectController@index')=>Lang::get('avalon::messages.objects'),
		Lang::get('avalon::messages.users'),
		)) }}

	<div class="btn-group">
		<a class="btn" href="{{ URL::action('UserController@create') }}"><i class="icon-plus"></i> {{ Lang::get('avalon::messages.users_create') }}</a>
	</div>

	<table class="table table-condensed">
		<thead>
		<tr>
			<th>Name</th>
			<th>Role</th>
			<th class="right">Last Login</th>
			<th class="active"></th>
		</tr>
		</thead>
		@foreach ($users as $user)
		<tr @if (!$user->active) class="inactive"@endif>
			<td><a href="{{ URL::action('UserController@edit', $user->id) }}">{{ $user->firstname }} {{ $user->lastname }}</a></td>
			<td>{{ $user->role }}</td>
			<td class="right">{{ $user->last_login }}</td>
			<td class="active">
				<a href="{{ URL::action('UserController@getActivate', array($user->id)) }}">
				@if (!$user->active)
					<i class="icon-check-empty"></i>
				@else
					<i class="icon-check"></i>
				@endif
				</a>
			</td>
		</tr>
		@endforeach
	</table>
@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.users_help') }}</p>
@endsection