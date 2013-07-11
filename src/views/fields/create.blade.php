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
		->class('required')
		}}
	
	{{ Former::select('type')
		->options($types, 'string')
		->label(Lang::get('avalon::messages.fields_type'))
		}}
	
	@if (count($related_objects))
	{{ Former::select('related_object_id')
		->addOption('', '')
		->fromQuery($related_objects, 'title')
		->label(Lang::get('avalon::messages.fields_related_object'))
		}}
	@endif
	
	@if (count($related_fields))
	{{ Former::select('related_field_id')
		->addOption('', '')
		->fromQuery($related_fields, 'title')
		->label(Lang::get('avalon::messages.fields_related_field'))
		}}
	@endif
	
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