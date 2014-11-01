@extends('avalon::template')

@section('title')
	@lang('avalon::messages.fields_create')
@endsection

@section('main')

	{{ Breadcrumbs::leave([
		URL::action('ObjectController@index')=>trans('avalon::messages.objects'),
		URL::action('InstanceController@index', $object->name)=>$object->title,
		URL::action('FieldController@index', $object->name)=>trans('avalon::messages.fields'),
		trans('avalon::messages.fields_create'),
		]) }}

	{{ Form::open(['class'=>'form-horizontal', 'url'=>URL::action('FieldController@store', $object->name)]) }}
	
	<div class="form-group">
		{{ Form::label('title', trans('avalon::messages.fields_title'), ['class'=>'control-label col-sm-2']) }}
	    <div class="col-sm-10">
			{{ Form::text('title', null, ['class'=>'required form-control', 'autofocus']) }}
	    </div>
	</div>

	<div class="form-group">
		{{ Form::label('type', trans('avalon::messages.fields_type'), ['class'=>'control-label col-sm-2']) }}
	    <div class="col-sm-10">
			{{ Form::select('type', $types, 'string', ['class'=>'form-control']) }}
	    </div>
	</div>
	
	@if (count($related_objects))
	<div class="form-group">
		{{ Form::label('related_object_id', trans('avalon::messages.fields_related_object'), ['class'=>'control-label col-sm-2']) }}
	    <div class="col-sm-10">
			{{ Form::select('related_object_id', $related_objects, null, ['class'=>'form-control']) }}
	    </div>
	</div>
	@endif
	
	@if (count($related_fields))
	<div class="form-group">
		{{ Form::label('related_field_id', trans('avalon::messages.fields_related_field'), ['class'=>'control-label col-sm-2']) }}
	    <div class="col-sm-10">
			{{ Form::select('related_field_id', $related_fields, null, ['class'=>'form-control']) }}
	    </div>
	</div>
	@endif
	
	<div class="form-group">
		{{ Form::label('width', trans('avalon::messages.fields_width'), ['class'=>'control-label col-sm-2']) }}
	    <div class="col-sm-10">
			{{ Form::text('width', null, ['class'=>'form-control']) }}
	    </div>
	</div>

	<div class="form-group">
		{{ Form::label('height', trans('avalon::messages.fields_height'), ['class'=>'control-label col-sm-2']) }}
	    <div class="col-sm-10">
			{{ Form::text('height', null, ['class'=>'form-control']) }}
	    </div>
	</div>

	<div class="form-group">
		{{ Form::label('visibility', trans('avalon::messages.fields_visibility'), ['class'=>'control-label col-sm-2']) }}
	    <div class="col-sm-10">
			{{ Form::select('visibility', $visibility, 'normal', ['class'=>'form-control']) }}
	    </div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<div class="checkbox">
				<label>
					{{ Form::checkbox('required') }}
					@lang('avalon::messages.fields_required')
				</label>
			</div>
		</div>
	</div>

	<div class="form-group">
	    <div class="col-sm-10 col-sm-offset-2">
			{{ Form::submit(trans('avalon::messages.site_save'), ['class'=>'btn btn-primary']) }}
			{{ HTML::link(URL::action('FieldController@index', $object->name), trans('avalon::messages.site_cancel'), ['class'=>'btn btn-default']) }}
	    </div>
	</div>
	
	{{ Form::close() }}
	
@endsection

@section('side')
	<p>@lang('avalon::messages.fields_create_help')</p>
@endsection