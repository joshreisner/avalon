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
		<a class="btn btn-default" href="{{ URL::action('UserController@create') }}"><i class="icon-plus"></i> {{ Lang::get('avalon::messages.users_create') }}</a>
	</div>

	{{ Table::rows($users)
		->column('name', 'string', Lang::get('avalon::messages.users_name'))
		->column('role', 'string', Lang::get('avalon::messages.users_role'))
		->column('last_login', 'date', Lang::get('avalon::messages.users_last_login'))
		->column('updated_at', 'updated_at', Lang::get('avalon::messages.site_updated_at'))
		->deletable()
		->draw()
		}}

@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.users_help') }}</p>
@endsection