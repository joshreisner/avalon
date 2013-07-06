@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.objects') }}
@endsection

@section('main')
	<h1 class="breadcrumbs">
		<a href="/"><i class="icon-home"></i></a>
		<i class="icon-chevron-right"></i>
		{{ Lang::get('avalon::messages.objects') }}
	</h1>
	
	<div class="btn-group">
		<!--<a class="btn" href="/login/settings"><i class="icon-cog"></i> {{ Lang::get('avalon::messages.site_settings') }}</a>-->
		<a class="btn" href="{{ URL::action('UserController@index') }}"><i class="icon-group"></i> {{ Lang::get('avalon::messages.users') }}</a>
		<a class="btn" href="{{ URL::action('ObjectController@create') }}"><i class="icon-plus"></i> {{ Lang::get('avalon::messages.objects_create') }}</a>
	</div>

	@if (count($objects))
	<table class="table table-condensed">
		<thead>
		<tr>
			<th>Object</th>
			<th class="center">Count</th>
			<th class="right">Updated</th>
		</tr>
		</thead>
		@foreach ($objects as $object)
		<tr>
			<td><a href="{{ URL::action('ObjectController@show', $object->id) }}">{{ $object->title }}</a></td>
			<td class="center">{{ $object->instance_count }}</td>
			<td class="right">{{ $object->instance_updated_at }}</td>
		</tr>
		@endforeach
	</table>
	@else
	<div class="alert">
		{{ Lang::get('avalon::messages.objects_empty') }}
	</div>
	@endif
@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.objects_help') }}</p>
	<p><a href="{{ URL::action('LoginController@logout') }}" class="btn btn-mini">{{ Lang::get('avalon::messages.site_logout') }}</a>
@endsection