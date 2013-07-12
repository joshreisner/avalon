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
		@if ($field->type == 'checkboxes')
			<div class="control-group">
			    <label class="control-label">{{ $field->title }}</label>
			    <div class="controls">
			    	@foreach ($options[$field->name]['options'] as $checkbox)
					<label class="checkbox">
						<input type="checkbox" name="{{ $field->name }}[]" value="{{ $checkbox->id }}"> {{ $checkbox->{$options[$field->name]['column_name']} }}
					</label>
					@endforeach
				</div>
			</div>
		@elseif ($field->type == 'date')
			{{ Former::input($field->name)
				->type('date')
				->label($field->title)
				->value($field->required ? date('Y-m-d') : false)
				->class($field->required ? 'date required' : 'date')
				->inlineHelp($field->help)
				}}
		@elseif ($field->type == 'datetime')
			{{ Former::input($field->name)
				->type('datetime-local')
				->label($field->title)
				->value($field->required ? date('Y-m-d\TH:i:s') : false)
				->class($field->required ? 'datetime required' : 'datetime')
				->inlineHelp($field->help)
				}}
		@elseif ($field->type == 'html')
			{{ Former::textarea($field->name)
				->label($field->title)
				->class($field->required ? 'html required' : 'html')
				->inlineHelp($field->help)
				}}
		@elseif ($field->type == 'select')
			@if ($field->required)
			{{ Former::select($field->name)
				->label($field->title)
				->fromQuery($options[$field->name]['options'], $options[$field->name]['column_name'])
				->inlineHelp($field->help)
				}}
			@else
			{{ Former::select($field->name)
				->label($field->title)
				->addOption('', '')
				->fromQuery($options[$field->name]['options'], $options[$field->name]['column_name'])
				->inlineHelp($field->help)
				}}
			@endif
		@elseif ($field->type == 'slug')
			{{ Former::text($field->name)
				->label($field->title)
				->class($field->required ? 'slug required' : 'slug')
				->inlineHelp($field->help)
				}}
		@elseif ($field->type == 'string')
			{{ Former::text($field->name)
				->label($field->title)
				->class($field->required ? 'string required' : 'string')
				->inlineHelp($field->help)
				}}
		@elseif ($field->type == 'text')
			{{ Former::textarea($field->name)
				->label($field->title)
				->class($field->required ? 'text required' : 'text')
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