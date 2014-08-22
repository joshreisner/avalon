@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.instances_edit') }}
@endsection

@section('main')

	{{ Breadcrumbs::leave(array(
		URL::action('ObjectController@index')=>Lang::get('avalon::messages.objects'),
		URL::action('InstanceController@index', $object->name)=>$object->title,
		Lang::get('avalon::messages.instances_edit'),
		)) }}

	{{ Form::open(array('class'=>'form-horizontal ' . $object->name, 'url'=>URL::action('InstanceController@update', array($object->name, $instance->id, $linked_id)), 'method'=>'put')) }}
	
	@if (Input::has('return_to'))
		{{ Form::hidden('return_to', Input::get('return_to')) }}
	@endif

	@foreach ($fields as $field)
		{{--
		@if ($linked_id && $field->id == $object->group_by_field)
			{{ Form::hidden($field->name, $linked_id) }}
		@else
		--}}
		@if ($field->type == 'checkboxes')
			<div class="form-group {{ $field->type }}">
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
			<div class="form-group {{ $field->type }}">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::text($field->name, $instance->{$field->name}, array('class'=>'form-control ' . $field->type . ' {hash:true,caps:false}' . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'date')
			<div class="form-group {{ $field->type }}">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
		            <div class="input-group date" data-date-format="MM/DD/YYYY">
		                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
		                <input type="text" class="form-control  @if ($field->required) required @endif" value="{{ date('m/d/Y', strtotime($instance->{$field->name})) }}" name="{{ $field->name }}">
		            </div>
			    </div>
			</div>
		@elseif ($field->type == 'datetime')
			<div class="form-group {{ $field->type }}">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
		            <div class="input-group datetime" data-date-format="MM/DD/YYYY hh:mm A">
		                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
		                <input type="text" class="form-control  @if ($field->required) required @endif" value="{{ date('m/d/Y h:i A', strtotime($instance->{$field->name})) }}" name="{{ $field->name }}">
		            </div>
			    </div>
			</div>
		@elseif ($field->type == 'html')
			<div class="form-group {{ $field->type }}">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::textarea($field->name, $instance->{$field->name}, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'image')
			<div class="form-group {{ $field->type }}">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
				<div class="col-sm-10">
					@if (empty($instance->{$field->name}))
					<div class="image_upload" id="image_{{ $field->id }}" style="width:{{ $field->screen_width }}px; height:{{ $field->screen_height }}px; line-height:{{ $field->screen_height }}px;">
						{{ $field->width or '&infin;' }} &times; {{ $field->height or '&infin;' }}
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
			<div class="form-group {{ $field->type }}">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::integer($field->name, $instance->{$field->name}, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'select')
			<div class="form-group {{ $field->type }}">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::select($field->name, $field->options, $instance->{$field->name}, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'slug')
			<div class="form-group {{ $field->type }}">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::text($field->name, $instance->{$field->name}, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'string')
			<div class="form-group {{ $field->type }}">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::text($field->name, $instance->{$field->name}, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'text')
			<div class="form-group {{ $field->type }}">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::textarea($field->name, $instance->{$field->name}, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'time')
			<div class="form-group {{ $field->type }}">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::time($field->name, $instance->{$field->name}, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'url')
			<div class="form-group {{ $field->type }}">
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
			@if (Input::has('return_to'))
			{{ HTML::link(Input::get('return_to'), Lang::get('avalon::messages.site_cancel'), array('class'=>'btn btn-default')) }}
			@else
			{{ HTML::link(URL::action('InstanceController@index', $object->name), Lang::get('avalon::messages.site_cancel'), array('class'=>'btn btn-default')) }}
			@endif
	    </div>
	</div>

	{{ Form::close() }}

	@foreach ($links as $link)

	<div class="related">
		<h3>{{ $link['object']->title }}</h3>

		<div class="btn-group">
			<a class="btn btn-default" id="create" href="{{ URL::action('InstanceController@create', array($link['object']->name, $instance->id)) }}"><i class="glyphicon glyphicon-plus"></i> {{ Lang::get('avalon::messages.instances_create') }}</a>
		</div>
		
		{{ InstanceController::table($link['object'], $link['fields'], $link['instances']) }}
	</div>
	
	@endforeach

@endsection

@section('side')
	@if (!empty($object->form_help))
		<p>{{ nl2br($object->form_help) }}</p>
	@endif

	{{ Form::open(array('method'=>'delete', 'action'=>array('InstanceController@destroy', $object->name, $instance->id))) }}
	<button type="submit" class="btn btn-default btn-xs">{{ Lang::get('avalon::messages.instances_destroy') }}</button>
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