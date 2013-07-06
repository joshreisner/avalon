@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.users') }}
@endsection

@section('main')
	<h1 class="breadcrumbs">
		<a href="/"><i class="icon-home"></i></a>
		<i class="icon-chevron-right"></i>
		<a href="{{ URL::action('ObjectController@index') }}">{{ Lang::get('avalon::messages.objects') }}</a>
		<i class="icon-chevron-right"></i>
		{{ Lang::get('avalon::messages.users') }}
	</h1>
	
	<div class="btn-group">
		<a class="btn" href="{{ URL::action('UserController@create') }}"><i class="icon-plus"></i> {{ Lang::get('avalon::messages.users_create') }}</a>
	</div>

	<table class="table table-condensed">
		<thead>
		<tr>
			<th>Name</th>
			<th>Role</th>
			<th class="right">Last Login</th>
		</tr>
		</thead>
		@foreach ($users as $user)
		<tr>
			<td><a href="{{ URL::action('UserController@edit', $user->id) }}">{{ $user->firstname }} {{ $user->lastname }}</a></td>
			<td>{{ $user->role }}</td>
			<td class="right">{{ $user->last_login }}</td>
		</tr>
		@endforeach
	</table>
@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.users_help') }}</p>
@endsection