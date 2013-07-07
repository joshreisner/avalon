@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.fields_create') }}
@endsection

@section('main')

	{{ Breadcrumbs::leave(array(
		URL::action('ObjectController@index')=>Lang::get('avalon::messages.objects'),
		URL::action('ObjectController@show', $object->id)=>$object->title,
		URL::action('FieldController@index', $object->id)=>Lang::get('avalon::messages.fields'),
		Lang::get('avalon::messages.fields_create'),
		)) }}

	{{ Former::horizontal_open()->action(URL::action('FieldController@store', $object->id)) }}
	
	{{ Former::text('title')
		->label(Lang::get('avalon::messages.fields_title'))
		->required()
		}}
	
	{{ Former::select('type')
		->options($types)
		->label(Lang::get('avalon::messages.fields_type'))
		}}
	
	{{ Former::select('visibility')
		->options($visibility)
		->label(Lang::get('avalon::messages.fields_visibility'))
		}}
	
	{{ Former::checkbox('required')
		->label(Lang::get('avalon::messages.fields_required'))
		}}
	
	{{ Former::actions()
		->primary_submit(Lang::get('avalon::messages.site_save'))
		->link(Lang::get('avalon::messages.site_cancel'), URL::action('FieldController@index', $object->id))
		}}
	
	{{ Former::close() }}
	
@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.fields_create_help') }}</p>
@endsection