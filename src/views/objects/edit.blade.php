@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.objects_edit', array('title'=>$object->title)) }}
@endsection

@section('main')

	{{ Breadcrumbs::leave(array(
		URL::action('ObjectController@index')=>Lang::get('avalon::messages.objects'),
		URL::action('InstanceController@index', $object->id)=>$object->title,
		Lang::get('avalon::messages.objects_edit'),
		)) }}

	{{ Form::open(array('class'=>'form-horizontal', 'url'=>URL::action('ObjectController@update', $object->id), 'method'=>'put')) }}
	
	<div class="form-group">
		{{ Form::label('title', Lang::get('avalon::messages.objects_title'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::text('title', $object->title, array('class'=>'required form-control', 'autofocus'=>'autofocus')) }}
	    </div>
	</div>
	
	<div class="form-group">
		{{ Form::label('list_grouping', Lang::get('avalon::messages.objects_list_grouping'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::text('list_grouping', $object->list_grouping, array('class'=>'form-control', 'data-provide'=>'typeahead', 'data-source'=>$list_groupings)) }}
	    </div>
	</div>
		
	<div class="form-group">
		{{ Form::label('name', Lang::get('avalon::messages.objects_name'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::text('name', $object->name, array('class'=>'required form-control')) }}
	    </div>
	</div>
		
	<div class="form-group">
		{{ Form::label('model', Lang::get('avalon::messages.objects_model'), array('class'=>'control-label col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::text('model', $object->model, array('class'=>'required form-control')) }}
	    </div>
	</div>
			
	<div class="form-group">
		{{ Form::label('order_by', Lang::get('avalon::messages.objects_order_by'), array('class'=>'control-label col-sm-2')) }}
		<div class="col-sm-10">
			{{ Form::select('order_by', $order_by, $object->order_by, array('class'=>'form-control')) }}			
		</div>
	</div>
	
	<div class="form-group">
		{{ Form::label('direction', Lang::get('avalon::messages.objects_direction'), array('class'=>'control-label col-sm-2')) }}
		<div class="col-sm-10">
			{{ Form::select('direction', $direction, $object->direction, array('class'=>'form-control')) }}			
		</div>
	</div>
		
	<!-- (not implemented yet)
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<div class="checkbox">
				<label>
					{{ Form::checkbox('singleton', 'on', $object->singleton) }} {{ Lang::get('avalon::messages.objects_singleton') }}
				</label>
			</div>
		</div>
	</div>
	-->

	@if (count($group_by_field))
	<div class="form-group">
		{{ Form::label('group_by_field', Lang::get('avalon::messages.objects_group_by'), array('class'=>'control-label col-sm-2')) }}
		<div class="col-sm-10">
			{{ Form::select('group_by_field', $group_by_field, $object->group_by_field, array('class'=>'form-control')) }}
		</div>
	</div>
	@endif

	@if (count($related_objects))
	<div class="form-group">
		{{ Form::label('group_by_field', Lang::get('avalon::messages.objects_related'), array('class'=>'control-label col-sm-2')) }}
		<div class="col-sm-10">
			@foreach ($related_objects as $related_object_id=>$related_object_title)
			<label class="checkbox-inline">
				{{ Form::checkbox('related_objects[]', $related_object_id, in_array($related_object_id, $links)) }} {{ $related_object_title }}
			</label>
			@endforeach
		</div>
	</div>
	@endif
	
	<div class="form-group">
		{{ Form::label('list_help', Lang::get('avalon::messages.objects_list_help'), array('class'=>'control-label col-sm-2')) }}
		<div class="col-sm-10">
			{{ Form::textarea('list_help', $object->list_help, array('class'=>'form-control')) }}			
		</div>
	</div>

	<div class="form-group">
		{{ Form::label('form_help', Lang::get('avalon::messages.objects_form_help'), array('class'=>'control-label col-sm-2')) }}
		<div class="col-sm-10">
			{{ Form::textarea('form_help', $object->form_help, array('class'=>'form-control')) }}			
		</div>
	</div>

	<div class="form-group">
	    <div class="col-sm-10 col-sm-offset-2">
			{{ Form::submit(Lang::get('avalon::messages.site_save'), array('class'=>'btn btn-primary')) }}
			{{ HTML::link(URL::action('InstanceController@index', $object->id), Lang::get('avalon::messages.site_cancel'), array('class'=>'btn btn-default')) }}
	    </div>
	</div>

	{{ Form::close() }}
	
@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.objects_edit_help', array('title'=>$object->title)) }}</p>

	@if (!$dependencies)
		{{ Form::open(array('method'=>'delete', 'action'=>array('ObjectController@destroy', $object->id))) }}
		<button type="submit" class="btn btn-default btn-xs">{{ Lang::get('avalon::messages.objects_destroy') }}</button>
		{{ Form::close() }}
	@else
		<p>{{ Lang::get('avalon::messages.objects_dependencies', array('dependencies', $dependencies)) }}</p>
	@endif

@endsection