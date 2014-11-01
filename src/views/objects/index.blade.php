@extends('avalon::template')

@section('title')
	{{ @trans('avalon::messages.objects') }}
@endsection

@section('main')

	{{ Breadcrumbs::leave([
		trans('avalon::messages.objects'),
		]) }}

	@if (Auth::user()->role < 3)
	<div class="btn-group">
		<a class="btn btn-default" href="{{ URL::action('UserController@index') }}">
			<i class="glyphicon glyphicon-user"></i>
			@lang('avalon::messages.users')
		</a>
		@if (Auth::user()->role < 2)
		<a class="btn btn-default" href="{{ URL::action('ObjectController@create') }}">
			<i class="glyphicon glyphicon-plus"></i>
			@lang('avalon::messages.objects_create')
		</a>
		@endif
	</div>
	@endif

	@if (count($objects))
		{{ Table::rows($objects)
			->column('title', 'string', trans('avalon::messages.object'))
			->column('count', 'integer', trans('avalon::messages.objects_count'))
			->column('updated_name', 'updated_name', trans('avalon::messages.site_updated_name'))
			->column('updated_at', 'updated_at', trans('avalon::messages.site_updated_at'))
			->groupBy('list_grouping')
			->draw('objects')
			}}
	@else
	<div class="alert alert-warning">
		@lang('avalon::messages.objects_empty')
	</div>
	@endif

@endsection

@section('side')
	<p>@lang('avalon::messages.objects_help')</p>
	<p><a href="{{ URL::action('LoginController@getLogout') }}" class="btn btn-default btn-xs">@lang('avalon::messages.site_logout')</a>
@endsection