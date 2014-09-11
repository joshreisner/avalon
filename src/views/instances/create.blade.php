@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.instances_create') }}
@endsection

@section('main')

	{{ Breadcrumbs::leave(array(
		URL::action('ObjectController@index')=>Lang::get('avalon::messages.objects'),
		URL::action('InstanceController@index', $object->name)=>$object->title,
		Lang::get('avalon::messages.instances_create'),
		)) }}

	{{ Form::open(array('class'=>'form-horizontal', 'url'=>URL::action('InstanceController@store', array($object->name, $linked_id)))) }}
	
	@if (Input::has('return_to'))
		{{ Form::hidden('return_to', Input::get('return_to')) }}
	@endif

	@foreach ($fields as $field)
		@if ($linked_id && $field->id == $object->group_by_field)
			{{ Form::hidden($field->name, $linked_id) }}
		@else
			<div class="form-group field-{{ $field->type }}">
				<label class="control-label col-sm-2">{{ $field->title }}</label>
				<div class="col-sm-10">
					@if ($field->type == 'checkbox')
						{{ Form::checkbox($field->name) }}
					@elseif ($field->type == 'checkboxes')
						@foreach ($field->options as $option_id=>$option_value)
							<label class="checkbox-inline">
								{{ Form::checkbox($field->name . '[]', $option_id) }}
								{{ $option_value }}
							</label>
						@endforeach
					@elseif ($field->type == 'color')
						{{ Form::text($field->name, $field->required ? '#ffffff' : null, array('class'=>'form-control ' . $field->type . ' {hash:true,caps:false}' . ($field->required ? ' required' : ''))) }}
					@elseif ($field->type == 'date')
						<div class="input-group date" data-date-format="MM/DD/YYYY">
							<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
							@if ($field->required)
							<input type="text" class="form-control required" value="{{ date('m/d/Y') }}" name="{{ $field->name }}">
						   	@else
							<input type="text" class="form-control" name="{{ $field->name }}">
						   	@endif
						</div>
					@elseif ($field->type == 'datetime')
						<div class="input-group datetime" data-date-format="MM/DD/YYYY hh:mm A">
							<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
							@if ($field->required)
							<input type="text" class="form-control required" value="{{ date('m/d/Y h:i A') }}" name="{{ $field->name }}">
						   	@else
							<input type="text" class="form-control" name="{{ $field->name }}">
						   	@endif
						</div>
					@elseif ($field->type == 'html')
						{{ Form::textarea($field->name, null, array('class'=>'form-control html' . ($field->required ? ' required' : ''))) }}
					@elseif ($field->type == 'image')
						{{ Form::hidden($field->name, null) }}
						<div class="image new" data-field-id="{{ $field->id }}" style="width:{{ $field->screen_width }}px; height:{{ $field->screen_height }}px; line-height:{{ $field->screen_height }}px;">
							<span>{{ $field->width or '&infin;' }} &times; {{ $field->height or '&infin;' }}</span>
						</div>
					@elseif ($field->type == 'images')
						{{ Form::hidden($field->name, null) }}
						<div class="image new" data-field-id="{{ $field->id }}" style="width:{{ $field->screen_width }}px; height:{{ $field->screen_height }}px; line-height:{{ $field->screen_height }}px;">
							<span>{{ $field->width or '&infin;' }} &times; {{ $field->height or '&infin;' }}</span>
						</div>
					@elseif ($field->type == 'integer')
						{{ Form::integer($field->name, null, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
					@elseif ($field->type == 'select')
						{{ Form::select($field->name, $field->options, null, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
					@elseif ($field->type == 'slug')
						{{ Form::text($field->name, null, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
					@elseif ($field->type == 'string')
						{{ Form::text($field->name, null, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
					@elseif ($field->type == 'text')
						{{ Form::textarea($field->name, null, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
					@elseif ($field->type == 'time')
						<div class="input-group time" data-date-format="hh:mm A">
							<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
							{{ Form::text($field->name, null, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
						</div>
					@elseif ($field->type == 'url')
						{{ Form::url($field->name, null, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
					@endif
				</div>
			</div>
		@endif
	@endforeach
	
	<div class="form-group">
		<div class="col-sm-10 col-sm-offset-2">
			{{ Form::submit(Lang::get('avalon::messages.site_save'), array('class'=>'btn btn-primary')) }}
			{{ HTML::link(URL::action('InstanceController@index', $object->name), Lang::get('avalon::messages.site_cancel'), array('class'=>'btn btn-default')) }}
		</div>
	</div>

	{{ Form::close() }}

@endsection

@section('side')
	@if (!empty($object->form_help))
		<p>{{ nl2br($object->form_help) }}</p>
	@endif
@endsection