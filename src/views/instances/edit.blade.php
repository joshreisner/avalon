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
			    <label class="control-label col-sm-2">{{ $field->title }}</label>
			    <div class="checkbox">
			    	@foreach ($options[$field->name]['options'] as $checkbox)
					<label class="checkbox">
						<input type="checkbox" name="{{ $field->name }}[]" value="{{ $checkbox->id }}" @if (in_array($checkbox->id, $options[$field->name]['values'])) checked="checked"@endif> {{ $checkbox->{$options[$field->name]['column_name']} }}
					</label>
					@endforeach
				</div>
			</div>
		@elseif ($field->type == 'color')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::text($field->name, $instance->{$field->name}, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'date')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::date($field->name, $instance->{$field->name}, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'datetime')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::datetime($field->name, $instance->{$field->name}, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'html')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::textarea($field->name, $instance->{$field->name}, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'image')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::hidden($field->name, $instance->{$field->name}) }}
					<img src="{{ $instance->{$field->name} }}" class="upload" data-field="{{ $field->id }}" alt="{{ $field->name }}">
			    </div>
			</div>
		@elseif ($field->type == 'integer')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::integer($field->name, $instance->{$field->name}, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'select')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::select($field->name, $options[$field->name]['options'], $instance->{$field->name}, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'slug')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::text($field->name, $instance->{$field->name}, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'string')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::text($field->name, $instance->{$field->name}, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'text')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::textarea($field->name, $instance->{$field->name}, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'time')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::time($field->name, $instance->{$field->name}, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'url')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::url($field->name, $instance->{$field->name}, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
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
	<button type="submit" class="btn btn-default btn-xs">{{ Lang::get('avalon::messages.instances_destroy') }}</button>
	{{ Form::close() }}

@endsection