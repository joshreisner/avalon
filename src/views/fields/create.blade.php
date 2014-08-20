@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.fields_create') }}
@endsection

@section('main')

	{{ Breadcrumbs::leave(array(
		URL::action('ObjectController@index')=>Lang::get('avalon::messages.objects'),
		URL::action('InstanceController@index', $object->name)=>$object->title,
		URL::action('FieldController@index', $object->name)=>Lang::get('avalon::messages.fields'),
		Lang::get('avalon::messages.fields_create'),
		)) }}

	{{ Form::open(array('class'=>'form-horizontal', 'url'=>URL::action('FieldController@store', $object->name))) }}
	
	<div class="form-group">
		{{ Form::label('title', Lang::get('avalon::messages.fields_title'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::text('title', null, array('class'=>'required form-control', 'autofocus'=>'autofocus')) }}
	    </div>
	</div>

	<div class="form-group">
		{{ Form::label('type', Lang::get('avalon::messages.fields_type'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::select('type', $types, 'string', array('class'=>'form-control')) }}
	    </div>
	</div>
	
	@if (count($related_objects))
	<div class="form-group">
		{{ Form::label('related_object_id', Lang::get('avalon::messages.fields_related_object'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::select('related_object_id', $related_objects, null, array('class'=>'form-control')) }}
	    </div>
	</div>
	@endif
	
	@if (count($related_fields))
	<div class="form-group">
		{{ Form::label('related_field_id', Lang::get('avalon::messages.fields_related_field'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::select('related_field_id', $related_fields, null, array('class'=>'form-control')) }}
	    </div>
	</div>
	@endif
	
	<div class="form-group">
		{{ Form::label('width', Lang::get('avalon::messages.fields_width'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::text('width', null, array('class'=>'form-control')) }}
	    </div>
	</div>

	<div class="form-group">
		{{ Form::label('height', Lang::get('avalon::messages.fields_height'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::text('height', null, array('class'=>'form-control')) }}
	    </div>
	</div>

	<div class="form-group">
		{{ Form::label('visibility', Lang::get('avalon::messages.fields_visibility'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::select('visibility', $visibility, 'normal', array('class'=>'form-control')) }}
	    </div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<div class="checkbox">
				<label>
					{{ Form::checkbox('required') }} {{ Lang::get('avalon::messages.fields_required') }}
				</label>
			</div>
		</div>
	</div>

	<div class="form-group">
	    <div class="col-sm-10 col-sm-offset-2">
			{{ Form::submit(Lang::get('avalon::messages.site_save'), array('class'=>'btn btn-primary')) }}
			{{ HTML::link(URL::action('FieldController@index', $object->id), Lang::get('avalon::messages.site_cancel'), array('class'=>'btn btn-default')) }}
	    </div>
	</div>
	
	{{ Form::close() }}
	
@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.fields_create_help') }}</p>
@endsection