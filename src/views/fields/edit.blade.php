@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.fields_edit') }}
@endsection

@section('main')

	{{ Breadcrumbs::leave(array(
		URL::action('ObjectController@index')=>Lang::get('avalon::messages.objects'),
		URL::action('ObjectController@show', $object->id)=>$object->title,
		URL::action('FieldController@index', $object->id)=>Lang::get('avalon::messages.fields'),
		Lang::get('avalon::messages.fields_edit'),
		)) }}

	{{ Former::horizontal_open()->action(URL::action('FieldController@update', array($object->id, $field->id)))->method('put') }}
	
	{{ Former::text('title')
		->label(Lang::get('avalon::messages.fields_title'))
		->value($field->title)
		->required()
		}}
	
	{{ Former::text('name')
		->label(Lang::get('avalon::messages.fields_name'))
		->value($field->name)
		->required()
		}}
	
	{{ Former::select('type')
		->options($types)
		->value($field->type)
		->label(Lang::get('avalon::messages.fields_type'))
		->disabled()
		}}
	
	{{ Former::select('visibility')
		->options($visibility)
		->value($field->visibility)
		->label(Lang::get('avalon::messages.fields_visibility'))
		}}
	
	@if ($field->required)
	{{ Former::checkbox('required')
		->checked()
		->label(Lang::get('avalon::messages.fields_required'))
		}}
	@else
	{{ Former::checkbox('required')
		->label(Lang::get('avalon::messages.fields_required'))
		}}
	@endif
	
	{{ Former::actions()
		->primary_submit(Lang::get('avalon::messages.site_save'))
		->link(Lang::get('avalon::messages.site_cancel'), URL::action('FieldController@index', $object->id))
		}}
	
	{{ Former::close() }}
	
@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.fields_edit_help') }}</p>
	{{ Form::open(array('method'=>'delete', 'action'=>array('FieldController@destroy', $object->id, $field->id))) }}
	<button type="submit" class="btn btn-mini">{{ Lang::get('avalon::messages.fields_destroy') }}</button>
	{{ Form::close() }}	
@endsection