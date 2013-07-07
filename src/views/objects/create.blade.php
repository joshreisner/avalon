@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.objects_create') }}
@endsection

@section('main')

	{{ Breadcrumbs::leave(array(
		URL::action('ObjectController@index')=>Lang::get('avalon::messages.objects'),
		Lang::get('avalon::messages.objects_create'),
		)) }}

	{{ Former::horizontal_open()->action(URL::action('ObjectController@store')) }}
	
	{{ Former::text('title')
		->label(Lang::get('avalon::messages.objects_title'))
		->required()
		->inlineHelp(Lang::get('avalon::messages.objects_title_help'))
		}}
	
	{{ Former::actions()
		->primary_submit(Lang::get('avalon::messages.site_save'))
		->link(Lang::get('avalon::messages.site_cancel'), URL::action('ObjectController@index'))
		}}
	
	{{ Former::close() }}
		
@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.objects_create_help') }}</p>
@endsection