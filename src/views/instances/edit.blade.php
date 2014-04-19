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
			    <div class="col-sm-10">
			    	@foreach ($field->options as $option_id=>$option_value)
					<label class="checkbox-inline">
						<input type="checkbox" name="{{ $field->name }}[]" value="{{ $option_id }}" @if (in_array($option_id, $instance->{$field->name})) checked="checked"@endif> {{ $option_value }}
					</label>
					@endforeach
				</div>
			</div>
		@elseif ($field->type == 'color')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::text($field->name, $instance->{$field->name}, array('class'=>'form-control ' . $field->type . ' {hash:true,caps:false}' . ($field->required ? ' required' : ''))) }}
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
					@if (empty($instance->{$field->name}))
					<div class="image_upload" id="image_{{ $field->id }}" style="width:{{ $field->screen_width }}px; height:{{ $field->screen_height }}px; line-height:{{ $field->screen_height }}px;">
						{{ $field->width }} &times; {{ $field->height }}
					</div>
					{{ Form::hidden($field->name, null) }}
					@else
					<div class="image_upload filled" id="image_{{ $field->id }}" style="width:{{ $field->screen_width }}px; height:{{ $field->screen_height }}px; line-height:{{ $field->screen_height }}px; background-image: url({{ $instance->{$field->name}->url }});">
						{{ $field->width }} &times; {{ $field->height }}
					</div>
					{{ Form::hidden($field->name, $instance->{$field->name}->id) }}
					@endif
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
					{{ Form::select($field->name, $field->options, $instance->{$field->name}, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
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