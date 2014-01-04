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

	{{ Form::open(array('class'=>'form-horizontal', 'url'=>URL::action('InstanceController@update', array($object->id, $instance->id)), 'method'=>'put')) }}
	
	@foreach ($fields as $field)
		@if ($field->type == 'checkboxes')
			<div class="form-group checkboxes">
			    <label class="col-sm-2">{{ $field->title }}</label>
			    <div class="checkbox">
			    	@foreach ($options[$field->name]['options'] as $checkbox)
					<label class="checkbox">
						<input type="checkbox" name="{{ $field->name }}[]" value="{{ $checkbox->id }}" @if (in_array($checkbox->id, $options[$field->name]['values'])) checked="checked"@endif> {{ $checkbox->{$options[$field->name]['column_name']} }}
					</label>
					@endforeach
				</div>
			</div>
		@elseif ($field->type == 'date')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::date($field->name, $instance->{$field->name}, array('class'=>$field->required ? 'form-control date required' : 'form-control date')) }}
			    </div>
			</div>
		@elseif ($field->type == 'datetime')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::datetime($field->name, $instance->{$field->name}, array('class'=>$field->required ? 'form-control datetime required' : 'form-control datetime')) }}
			    </div>
			</div>
		@elseif ($field->type == 'html')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::textarea($field->name, $instance->{$field->name}, array('class'=>$field->required ? 'form-control html required' : 'form-control html')) }}
			    </div>
			</div>
		@elseif ($field->type == 'images')
			<div class="control-group images">
			    <label class="control-label">{{ $field->title }}</label>
			    <div class="controls well">
					<input type="hidden" name="{{ $field->name }}" value="{{ @$instance->{$field->name} }}">
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
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::text($field->name, $instance->{$field->name}, array('class'=>$field->required ? 'form-control slug required' : 'form-control slug')) }}
			    </div>
			</div>
		@elseif ($field->type == 'string')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::text($field->name, $instance->{$field->name}, array('class'=>$field->required ? 'form-control string required' : 'form-control string')) }}
			    </div>
			</div>
		@elseif ($field->type == 'text')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::textarea($field->name, $instance->{$field->name}, array('class'=>$field->required ? 'form-control text required' : 'form-control text')) }}
			    </div>
			</div>
		@elseif ($field->type == 'url')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::url($field->name, $instance->{$field->name}, array('class'=>$field->required ? 'form-control url required' : 'form-control url')) }}
			    </div>
			</div>
		@endif
	@endforeach
	
	<div class="form-group">
	    <div class="col-sm-10 col-sm-offset-2">
			{{ Form::submit(Lang::get('avalon::messages.site_save'), array('class'=>'btn btn-primary')) }}
			{{ HTML::link(URL::action('InstanceController@index', $object->id), Lang::get('avalon::messages.site_cancel'), array('class'=>'btn btn-default')) }}
	    </div>
	</div>

	{{ Form::close() }}

@endsection

@section('side')
	<p>{{ nl2br($object->form_help) }}</p>

	{{ Form::open(array('method'=>'delete', 'action'=>array('InstanceController@destroy', $object->id, $instance->id))) }}
	<button type="submit" class="btn btn-mini">{{ Lang::get('avalon::messages.instances_destroy') }}</button>
	{{ Form::close() }}

	<!-- hidden image upload form -->
	{{ Form::open(array('method'=>'post', 'class'=>'upload', 'files'=>true, 'action'=>array('InstanceController@upload_image', $object->id, $instance->id))) }}
	<input type="hidden" name="field_id" value="41">
	<input type="hidden" name="filename">
	<input type="file" name="image_upload" id="image_upload">
	{{ Form::close() }}

@endsection