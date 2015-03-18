@extends('avalon::template')

@section('title')
	@lang('avalon::messages.instances_edit')
@endsection

@section('main')

	{{ Breadcrumbs::leave([
		URL::action('ObjectController@index')=>trans('avalon::messages.objects'),
		URL::action('InstanceController@index', $object->name)=>$object->title,
		trans('avalon::messages.instances_edit'),
		]) }}

	{{ Form::open(['class'=>'form-horizontal ' . $object->name, 'url'=>URL::action('InstanceController@update', [$object->name, $instance->id, $linked_id]), 'method'=>'put']) }}
	
		{{ Form::hidden('return_to', $return_to) }}

	@foreach ($fields as $field)
		{{--
		@if ($linked_id && $field->id == $object->group_by_field)
			{{ Form::hidden($field->name, $linked_id) }}
		@else
		--}}
		<div class="form-group field-{{ $field->type }}">
			<label class="control-label col-sm-2">{{ $field->title }}</label>
			<div class="col-sm-10">
				@if ($field->type == 'checkbox')
					{{ Form::checkbox($field->name, null, $instance->{$field->name}) }}
				@elseif ($field->type == 'checkboxes')
					@foreach ($field->options as $option_id=>$option_value)
					<label class="checkbox-inline">
						{{ Form::checkbox($field->name . '[]', $option_id, in_array($option_id, $instance->{$field->name})) }}
						{{ $option_value }}
					</label>
					@endforeach
				@elseif ($field->type == 'color')
					{{ Form::text($field->name, $instance->{$field->name}, ['class'=>'form-control ' . $field->type . ' {hash:true,caps:false}' . ($field->required ? ' required' : '')]) }}
				@elseif ($field->type == 'date')
					<div class="input-group date" data-date-format="MM/DD/YYYY">
						<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
						<input type="text" class="form-control  @if ($field->required) required @endif" value="{{ date('m/d/Y', strtotime($instance->{$field->name})) }}" name="{{ $field->name }}">
					</div>
				@elseif ($field->type == 'datetime')
					<div class="input-group datetime" data-date-format="MM/DD/YYYY hh:mm A">
						<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
						<input type="text" class="form-control  @if ($field->required) required @endif" value="{{ $instance->{$field->name} }}" name="{{ $field->name }}">
					</div>
				@elseif ($field->type == 'email')
					{{ Form::email($field->name, $instance->{$field->name}, ['class'=>'form-control ' . $field->type . ($field->required ? ' required' : '')]) }}
				@elseif ($field->type == 'html')
					{{ Form::textarea($field->name, $instance->{$field->name}, ['class'=>'form-control ' . $field->type . ($field->required ? ' required' : '')]) }}
				@elseif ($field->type == 'image')
					@if (empty($instance->{$field->name}))
					<div class="image new" data-field-id="{{ $field->id }}" style="width:{{ $field->screen_width }}px; height:{{ $field->screen_height }}px; line-height:{{ $field->screen_height }}px;">
						{{ $field->width or '&infin;' }} &times; {{ $field->height or '&infin;' }}
					</div>
					{{ Form::hidden($field->name, null) }}
					@else
					<div class="image" data-field-id="{{ $field->id }}" style="width:{{ $field->screen_width }}px; height:{{ $field->screen_height }}px; line-height:{{ $field->screen_height }}px; background-image: url({{ $instance->{$field->name}->url }});">
						{{ $field->width }} &times; {{ $field->height }}
					</div>
					{{ Form::hidden($field->name, $instance->{$field->name}->id) }}
					@endif
				@elseif ($field->type == 'images')
					<?php $ids = []; ?>
					@foreach ($instance->{$field->name} as $image)
						<div class="image" data-file-id="{{ $image->id }}" data-field-id="{{ $field->id }}" style="width:{{ $field->screen_width }}px; height:{{ $field->screen_height }}px; line-height:{{ $field->screen_height }}px; background-image: url({{ $image->url }});">
							{{ $field->width }} &times; {{ $field->height }}
						</div>
						<?php $ids[] = $image->id; ?>
					@endforeach
					{{ Form::hidden($field->name, implode(',', $ids)) }}
					<div class="image new" data-field-id="{{ $field->id }}" style="width:{{ $field->screen_width }}px; height:{{ $field->screen_height }}px; line-height:{{ $field->screen_height }}px;">
						{{ $field->width or '&infin;' }} &times; {{ $field->height or '&infin;' }}
					</div>
				@elseif ($field->type == 'integer')
					{{ Form::integer($field->name, $instance->{$field->name}, ['class'=>'form-control ' . $field->type . ($field->required ? ' required' : '')]) }}
				@elseif ($field->type == 'money')
					{{ Form::decimal($field->name, $instance->{$field->name}, ['class'=>'form-control ' . $field->type . ($field->required ? ' required' : '')]) }}
				@elseif ($field->type == 'select')
					{{ Form::select($field->name, $field->options, $instance->{$field->name}, ['class'=>'form-control ' . $field->type . ($field->required ? ' required' : '')]) }}
				@elseif ($field->type == 'slug')
					{{ Form::text($field->name, $instance->{$field->name}, ['class'=>'form-control ' . $field->type . ($field->required ? ' required' : '')]) }}
				@elseif ($field->type == 'string')
					{{ Form::text($field->name, $instance->{$field->name}, ['class'=>'form-control ' . $field->type . ($field->required ? ' required' : '')]) }}
				@elseif ($field->type == 'text')
					{{ Form::textarea($field->name, $instance->{$field->name}, ['class'=>'form-control ' . $field->type . ($field->required ? ' required' : '')]) }}
				@elseif ($field->type == 'time')
					<div class="input-group time" data-date-format="hh:mm A">
						<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
						{{ Form::text($field->name, $instance->{$field->name}, ['class'=>'form-control ' . $field->type . ($field->required ? ' required' : '')]) }}
					</div>
				@elseif ($field->type == 'url')
					{{ Form::url($field->name, $instance->{$field->name}, ['class'=>'form-control ' . $field->type . ($field->required ? ' required' : '')]) }}
				@elseif ($field->type == 'user')
					{{ Form::select($field->name, $field->options, $instance->{$field->name}, ['class'=>'form-control ' . $field->type . ($field->required ? ' required' : '')]) }}
				@elseif ($field->type == 'us_state')
					{{ Form::select($field->name, $field->options, $instance->{$field->name}, ['class'=>'form-control ' . $field->type . ($field->required ? ' required' : '')]) }}
				@elseif ($field->type == 'zip')
					{{ Form::text($field->name, $instance->{$field->name}, ['class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''), 'maxlength'=>5]) }}
				@endif
			</div>
		</div>
	@endforeach
	
	@if (!empty($object->url))
		<div class="form-group field-slug">
			<label class="control-label col-sm-2">Location</label>
			<div class="col-sm-10">
				<div class="input-group">
					<span class="input-group-addon">{{ url($object->url) }}/</span>
					<input type="text" name="slug" class="form-control slug" value="{{ $instance->slug }}">
					<span class="input-group-addon"><a href="{{ $object->url }}{{ $instance->slug }}" target="_blank"><i class="glyphicon glyphicon-new-window"></i></a></span>
				</div>
			</div>
		</div>
	@endif

	<div class="form-group">
		<div class="col-sm-10 col-sm-offset-2">
			{{ Form::submit(trans('avalon::messages.site_save'), ['class'=>'btn btn-primary']) }}
			{{ HTML::link($return_to, trans('avalon::messages.site_cancel'), ['class'=>'btn btn-default']) }}
		</div>
	</div>

	{{ Form::close() }}

	@foreach ($links as $link)

	<div class="related">
		<h3>{{ $link['object']->title }}</h3>

		<div class="btn-group">
			<a class="btn btn-default" id="create" href="{{ URL::action('InstanceController@create', [$link['object']->name, $instance->id]) }}">
				<i class="glyphicon glyphicon-plus"></i> 
				@lang('avalon::messages.instances_create')
			</a>
		</div>
		
		{{ InstanceController::table($link['object'], $link['fields'], $link['instances']) }}
	</div>
	
	@endforeach

@endsection

@section('side')
	@if (!empty($object->form_help))
		<p>{{ nl2br($object->form_help) }}</p>
	@endif

	{{ Form::open(['method'=>'delete', 'action'=>['InstanceController@destroy', $object->name, $instance->id]]) }}
		{{ Form::hidden('return_to', $return_to) }}
		{{ Form::submit(trans('avalon::messages.instances_destroy'), ['class'=>'btn btn-default btn-xs']) }}
	{{ Form::close() }}

@endsection

@section('script')
	<script>
	@if (Session::has('instance_id'))
		var $el = $("table tr#{{ Session::get('instance_id') }}");
		$el
			.after("<div class='highlight'/>")
			.next()
			.width($el.width())
			.height($el.height())
			.css("marginTop", -$el.height())
			.fadeOut(500, function(){
				$("div.highlight").remove();
			});
	@endif
	</script>
@endsection