@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.instances_create') }}
@endsection

@section('main')

	{{ Breadcrumbs::leave(array(
		URL::action('ObjectController@index')=>Lang::get('avalon::messages.objects'),
		URL::action('ObjectController@show', $object->id)=>$object->title,
		Lang::get('avalon::messages.instances_create'),
		)) }}

	{{ Former::horizontal_open()->action(URL::action('InstanceController@store', $object->id)) }}
	
	@foreach ($fields as $field)
		@if ($field->type == 'date')
			{{ Former::input($field->name)
				->type('date')
				->label($field->title)
				->value($field->required ? date('Y-m-d') : false)
				->inlineHelp($field->help)
				}}
		@elseif ($field->type == 'datetime')
			{{ Former::input($field->name)
				->type('datetime-local')
				->label($field->title)
				->value($field->required ? date('Y-m-d\TH:i:s') : false)
				->inlineHelp($field->help)
				}}
		@elseif ($field->type == 'html')
			{{ Former::textarea($field->name)
				->label($field->title)
				->class('redactor')
				->inlineHelp($field->help)
				}}
		@elseif ($field->type == 'string')
			{{ Former::text($field->name)
				->label($field->title)
				->class($field->required ? 'required' : '')
				->inlineHelp($field->help)
				}}
		@elseif ($field->type == 'text')
			{{ Former::textarea($field->name)
				->label($field->title)
				->class($field->required ? 'required' : '')
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
@endsection