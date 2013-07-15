@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.objects') }}
@endsection

@section('main')

	{{ Breadcrumbs::leave(array(
		Lang::get('avalon::messages.objects'),
		)) }}

	<div class="btn-group">
		<!--<a class="btn" href="/login/settings"><i class="icon-cog"></i> {{ Lang::get('avalon::messages.site_settings') }}</a>-->
		<a class="btn" href="{{ URL::action('UserController@index') }}"><i class="icon-group"></i> {{ Lang::get('avalon::messages.users') }}</a>
		<a class="btn" href="{{ URL::action('ObjectController@create') }}"><i class="icon-plus"></i> {{ Lang::get('avalon::messages.objects_create') }}</a>
	</div>

	@if (count($objects))
		{{ Table::rows($objects)
			->column('title', 'string', Lang::get('avalon::messages.object'))
			->column('instance_count', 'integer', Lang::get('avalon::messages.objects_count'))
			->column('instance_updated_at', 'updated', Lang::get('avalon::messages.site_updated'))
			->groupBy('list_grouping')
			->draw()
			}}
	@else
	<div class="alert">
		{{ Lang::get('avalon::messages.objects_empty') }}
	</div>
	@endif
@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.objects_help') }}</p>
	<p><a href="{{ URL::action('LoginController@getLogout') }}" class="btn btn-mini">{{ Lang::get('avalon::messages.site_logout') }}</a>
@endsection