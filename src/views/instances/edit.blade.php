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
		@elseif ($field->type == 'html')
			{{ Former::textarea($field->name)
				->label($field->title)
				->class('redactor')
				->value($instance->{$field->name})
				->inlineHelp($field->help)
				}}
		@elseif ($field->type == 'datetime')
			{{ Former::input($field->name)
				->type('datetime-local')
				->label($field->title)
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
	<p>{{ nl2br($object->form_help) }}</p>
	{{ Form::open(array('method'=>'delete', 'action'=>array('InstanceController@destroy', $object->id, $instance->id))) }}
	<button type="submit" class="btn btn-mini">{{ Lang::get('avalon::messages.instances_destroy') }}</button>
	{{ Form::close() }}	

@endsection