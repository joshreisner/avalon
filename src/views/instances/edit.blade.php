@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.instances_edit') }}
@endsection

@section('main')

	{{ Breadcrumbs::leave(array(
		URL::action('ObjectController@index')=>Lang::get('avalon::messages.objects'),
		URL::action('ObjectController@show', $object->id)=>$object->title,
		Lang::get('avalon::messages.instances_edit'),
		)) }}

	{{ Former::horizontal_open()->action(URL::action('InstanceController@update', array($object->id, $instance->id)))->method('put') }}
	
	@foreach ($fields as $field)
		@if ($field->type == 'string')
			{{ Former::text($field->name)
				->label($field->title)
				->class($field->required ? 'required' : '')
				->value($instance->{$field->name})
				->inlineHelp($field->help)
				}}
		@elseif ($field->type == 'text')
			{{ Former::textarea($field->name)
				->label($field->title)
				->class($field->required ? 'required' : '')
				->value($instance->{$field->name})
				->inlineHelp($field->help)
				}}
		@endif
	@endforeach
	
	{{ Former::actions()
		->primary_submit(Lang::get('avalon::messages.site_save'))
		->link(Lang::get('avalon::messages.site_cancel'), URL::action('ObjectController@show', $object->id))
		}}
	
	{{ Former::close() }}

@endsection

@section('side')
	<p>{{ $object->form_help }}</p>
@endsection