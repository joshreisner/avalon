@extends('avalon::template')

@section('title')
	{{ @trans('avalon::messages.import') }}
@endsection

@section('main')
	{{ Breadcrumbs::leave([
		URL::action('ObjectController@index')=>trans('avalon::messages.objects'),
		trans('avalon::messages.import'),
		]) }}

	{{ Table::rows($tables)
	->column('Name', 'string', trans('avalon::messages.import_table'))
	->column('Rows', 'integer', trans('avalon::messages.import_rows'))
	->column('Data_length', 'integer', trans('avalon::messages.import_size'))
	->draw('tables')
	}}
@endsection