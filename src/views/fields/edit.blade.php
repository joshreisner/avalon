@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.fields_edit') }}
@endsection

@section('main')

	{{ Breadcrumbs::leave(array(
		URL::action('ObjectController@index')=>Lang::get('avalon::messages.objects'),
		URL::action('InstanceController@index', $object->id)=>$object->title,
		URL::action('FieldController@index', $object->id)=>Lang::get('avalon::messages.fields'),
		Lang::get('avalon::messages.fields_edit'),
		)) }}

	{{ Form::open(array('class'=>'form-horizontal', 'url'=>URL::action('FieldController@update', array($object->id, $field->id)), 'method'=>'put')) }}
	
	<div class="form-group">
		{{ Form::label('title', Lang::get('avalon::messages.fields_title'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::text('title', $field->title, array('class'=>'required form-control', 'autofocus'=>'autofocus')) }}
	    </div>
	</div>
	
	<div class="form-group">
		{{ Form::label('name', Lang::get('avalon::messages.fields_name'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::text('name', $field->name, array('class'=>'required form-control')) }}
	    </div>
	</div>

	<div class="form-group">
		{{ Form::label('type', Lang::get('avalon::messages.fields_type'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::select('type', $types, $field->type, array('class'=>'form-control', 'disabled')) }}
	    </div>
	</div>
			
	@if (count($related_objects))
	<div class="form-group">
		{{ Form::label('related_object_id', Lang::get('avalon::messages.fields_related_object'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::select('related_object_id', $related_objects, $field->related_object_id, array('class'=>'form-control')) }}
	    </div>
	</div>
	@endif
	
	@if (count($related_fields))
	<div class="form-group">
		{{ Form::label('related_field_id', Lang::get('avalon::messages.fields_related_field'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::select('related_field_id', $related_fields, $field->related_field_id, array('class'=>'form-control')) }}
	    </div>
	</div>
	@endif
	
	<div class="form-group">
		{{ Form::label('visibility', Lang::get('avalon::messages.fields_visibility'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::select('visibility', $visibility, $field->visibility, array('class'=>'form-control')) }}
	    </div>
	</div>

	<div class="form-group">
		{{ Form::label('width', Lang::get('avalon::messages.fields_width'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::text('width', $field->width, array('class'=>'form-control')) }}
	    </div>
	</div>

	<div class="form-group">
		{{ Form::label('height', Lang::get('avalon::messages.fields_height'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::text('height', $field->height, array('class'=>'form-control')) }}
	    </div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<div class="checkbox">
				<label>
					{{ Form::checkbox('required', 'on', $field->required) }} {{ Lang::get('avalon::messages.fields_required') }}
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
	<p>{{ Lang::get('avalon::messages.fields_edit_help') }}</p>

	@if ($object->order_by == $field->name)
		<p>{{ Lang::get('avalon::messages.fields_not_deletable') }}</p>
	@else
		{{ Form::open(array('method'=>'delete', 'action'=>array('FieldController@destroy', $object->id, $field->id))) }}
		<button type="submit" class="btn btn-default btn-xs">{{ Lang::get('avalon::messages.fields_destroy') }}</button>
		{{ Form::close() }}	
	@endif

@endsection