@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.instances_edit') }}
@endsection

@section('main')

	{{ Breadcrumbs::leave(array(
		URL::action('ObjectController@index')=>Lang::get('avalon::messages.objects'),
		URL::action('InstanceController@index', $object->id)=>$object->title,
		Lang::get('avalon::messages.instances_edit'),
		)) }}

	{{ Former::horizontal_open()->action(URL::action('InstanceController@update', array($object->id, $instance->id)))->method('put') }}
	
	@foreach ($fields as $field)
		@if ($field->type == 'checkboxes')
			<div class="control-group checkboxes">
			    <label class="control-label">{{ $field->title }}</label>
			    <div class="controls">
			    	@foreach ($options[$field->name]['options'] as $checkbox)
					<label class="checkbox">
						<input type="checkbox" name="{{ $field->name }}[]" value="{{ $checkbox->id }}" @if (in_array($checkbox->id, $options[$field->name]['values'])) checked="checked"@endif> {{ $checkbox->{$options[$field->name]['column_name']} }}
					</label>
					@endforeach
				</div>
			</div>
		@elseif ($field->type == 'date')
			{{ Former::input($field->name)
				->type('date')
				->class($field->required ? 'date required' : 'date')
				->label($field->title)
				->value($instance->{$field->name})
				->inlineHelp($field->help)
				}}
		@elseif ($field->type == 'datetime')
			{{ Former::input($field->name)
				->type('datetime-local')
				->class($field->required ? 'datetime required' : 'datetime')
				->label($field->title)
				->value($instance->{$field->name})
				->inlineHelp($field->help)
				}}
		@elseif ($field->type == 'html')
			{{ Former::textarea($field->name)
				->label($field->title)
				->class($field->required ? 'html required' : 'html')
				->value($instance->{$field->name})
				->inlineHelp($field->help)
				}}
		@elseif ($field->type == 'images')
			<div class="control-group images">
			    <label class="control-label">{{ $field->title }}</label>
			    <div class="controls well">
			    	<!--<div class="image add">
			    		<i class="icon-picture"></i>
			    		<div class="dimension width">830</div>
			    		<div class="dimension height">&infin;</div>
			    	</div>-->
				</div>
			</div>
		@elseif ($field->type == 'select')
			@if ($field->required)
			{{ Former::select($field->name)
				->label($field->title)
				->fromQuery($options[$field->name]['options'], $options[$field->name]['column_name'])
				->value($instance->{$field->name})
				->inlineHelp($field->help)
				}}
			@else
			{{ Former::select($field->name)
				->label($field->title)
				->addOption('', '')
				->fromQuery($options[$field->name]['options'], $options[$field->name]['column_name'])
				->value($instance->{$field->name})
				->inlineHelp($field->help)
				}}
			@endif
		@elseif ($field->type == 'slug')
			{{ Former::text($field->name)
				->label($field->title)
				->class($field->required ? 'slug required' : 'slug')
				->value($instance->{$field->name})
				->inlineHelp($field->help)
				}}
		@elseif ($field->type == 'string')
			{{ Former::text($field->name)
				->label($field->title)
				->class($field->required ? 'string required' : 'string')
				->value($instance->{$field->name})
				->inlineHelp($field->help)
				}}
		@elseif ($field->type == 'text')
			{{ Former::textarea($field->name)
				->label($field->title)
				->class($field->required ? 'text required' : 'text')
				->value($instance->{$field->name})
				->inlineHelp($field->help)
				}}
		@elseif ($field->type == 'url')
			{{ Former::url($field->name)
				->label($field->title)
				->class($field->required ? 'url required' : 'url')
				->placeholder(Lang::get('avalon::messages.fields_url_placeholder'))
				->value($instance->{$field->name})
				->inlineHelp($field->help)
				}}
		@endif
	@endforeach
	
	{{ Former::actions()
		->primary_submit(Lang::get('avalon::messages.site_save'))
		->link(Lang::get('avalon::messages.site_cancel'), URL::action('InstanceController@index', $object->id))
		}}
	
	{{ Former::close() }}

@endsection

@section('side')
	<p>{{ nl2br($object->form_help) }}</p>

	{{ Form::open(array('method'=>'delete', 'action'=>array('InstanceController@destroy', $object->id, $instance->id))) }}
	<button type="submit" class="btn btn-mini">{{ Lang::get('avalon::messages.instances_destroy') }}</button>
	{{ Form::close() }}

	<!-- hidden image upload form -->
	{{ Form::open(array('method'=>'post', 'class'=>'upload', 'files'=>true, 'action'=>array('InstanceController@upload_image', $object->id, $instance->id))) }}
	<input type="hidden" name="field_id" value="41">
	<input type="file" name="image_upload" id="image_upload">
	{{ Form::close() }}

@endsection