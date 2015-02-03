@extends('avalon::template')

@section('title')
	{{ @trans('avalon::messages.import') }}
@endsection

@section('main')
	{{ Breadcrumbs::leave([
		URL::action('ObjectController@index')=>trans('avalon::messages.objects'),
		URL::action('ImportController@index')=>trans('avalon::messages.import'),
		$table,
		]) }}

	@if (!empty($html))
		{{ $html }}
	@else
	<div class="alert alert-warning">
		@lang('avalon::messages.import_table_empty')
	</div>
	@endif
@endsection

@section('side')
	<p>@lang('avalon::messages.import_table_help')</p>
	<p><a href="{{ URL::action('ImportController@drop', $table) }}" class="btn btn-default btn-xs">@lang('avalon::messages.import_table_drop')</a>

@endsection