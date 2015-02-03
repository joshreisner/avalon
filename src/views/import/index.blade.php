@extends('avalon::template')

@section('title')
	{{ @trans('avalon::messages.import') }}
@endsection

@section('main')
	{{ Breadcrumbs::leave([
		URL::action('ObjectController@index')=>trans('avalon::messages.objects'),
		trans('avalon::messages.import'),
		]) }}

	@if (count($tables))
		{{ Table::rows($tables)
		->column('Name', 'string', trans('avalon::messages.import_table'))
		->column('Rows', 'integer', trans('avalon::messages.import_rows'))
		->column('Data_length', 'integer', trans('avalon::messages.import_size'))
		->draw('tables')
		}}
	@else
	<div class="alert alert-warning">
		@lang('avalon::messages.import_empty')
	</div>
	@endif
@endsection

@section('side')
	<p>@lang('avalon::messages.import_help')</p>
@endsection