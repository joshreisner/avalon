@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.instances_create') }}
@endsection

@section('main')

	{{ Breadcrumbs::leave(array(
		URL::action('ObjectController@index')=>Lang::get('avalon::messages.objects'),
		URL::action('InstanceController@index', $object->id)=>$object->title,
		Lang::get('avalon::messages.instances_create'),
		)) }}

	{{ Form::open(array('class'=>'form-horizontal', 'url'=>URL::action('InstanceController@store', array($object->id, $linked_id)))) }}
	
	@if (Input::has('return_to'))
		{{ Form::hidden('return_to', Input::get('return_to')) }}
	@endif

	@foreach ($fields as $field)
		@if ($linked_id && $field->id == $object->group_by_field)
			{{ Form::hidden($field->name, $linked_id) }}
		@elseif ($field->type == 'checkboxes')
			<div class="form-group">
			    <label class="control-label col-sm-2">{{ $field->title }}</label>
			    <div class="col-sm-10">
			    	@foreach ($field->options as $option_id=>$option_value)
						<label class="checkbox-inline">
							<input type="checkbox" name="{{ $field->name }}[]" value="{{ $option_id }}">
							{{ $option_value }}
						</label>
					@endforeach
				</div>
			</div>
		@elseif ($field->type == 'color')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::text($field->name, $field->required ? '#ffffff' : null, array('class'=>'form-control ' . $field->type . ' {hash:true,caps:false}' . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'date')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
		            <div class="input-group date" data-date-format="MM/DD/YYYY">
		                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
		                @if ($field->required)
		                <input type="text" class="form-control required" value="{{ date('m/d/Y') }}" name="{{ $field->name }}">
		               	@else
		                <input type="text" class="form-control" name="{{ $field->name }}">
		               	@endif
		            </div>
					<!--{{ Form::date($field->name, $field->required ? date('Y-m-d\TH:i:s') : null, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}-->
			    </div>
			</div>
		@elseif ($field->type == 'datetime')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
		            <div class="input-group datetime" data-date-format="MM/DD/YYYY hh:mm A">
		                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
		                @if ($field->required)
		                <input type="text" class="form-control required" value="{{ date('m/d/Y h:i A') }}" name="{{ $field->name }}">
		               	@else
		                <input type="text" class="form-control" name="{{ $field->name }}">
		               	@endif
		            </div>
					<!--{{ Form::datetime($field->name, $field->required ? date('Y-m-d\TH:i:s') : null, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}-->
			    </div>
			</div>
		@elseif ($field->type == 'html')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::textarea($field->name, null, array('class'=>'form-control html' . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'image')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
				<div class="col-sm-10">
					<div class="image_upload" id="image_{{ $field->id }}" style="width:{{ $field->screen_width }}px; height:{{ $field->screen_height }}px; line-height:{{ $field->screen_height }}px;">
						{{ $field->width or '&infin;' }} &times; {{ $field->height or '&infin;' }}
					</div>
					{{ Form::hidden($field->name, null) }}
				</div>
			</div>
		@elseif ($field->type == 'integer')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::integer($field->name, null, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'select')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::select($field->name, $field->options, null, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'slug')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::text($field->name, null, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'string')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::text($field->name, null, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'text')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::textarea($field->name, null, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'time')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::time($field->name, null, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
			    </div>
			</div>
		@elseif ($field->type == 'url')
			<div class="form-group">
				{{ Form::label($field->name, $field->title, array('class'=>'control-label col-sm-2')) }}
			    <div class="col-sm-10">
					{{ Form::url($field->name, null, array('class'=>'form-control ' . $field->type . ($field->required ? ' required' : ''))) }}
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
@endsection