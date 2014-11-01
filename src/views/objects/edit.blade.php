@extends('avalon::template')

@section('title')
	@lang('avalon::messages.objects_edit', ['title'=>$object->title])
@endsection

@section('main')

	{{ Breadcrumbs::leave([
		URL::action('ObjectController@index')=>trans('avalon::messages.objects'),
		URL::action('InstanceController@index', $object->name)=>$object->title,
		trans('avalon::messages.objects_edit'),
		]) }}

	{{ Form::open(['class'=>'form-horizontal', 'url'=>URL::action('ObjectController@update', $object->name), 'method'=>'put']) }}
	
	<div class="form-group">
		{{ Form::label('title', trans('avalon::messages.objects_title'), ['class'=>'control-label col-sm-2']) }}
	    <div class="col-sm-10">
			{{ Form::text('title', $object->title, ['class'=>'required form-control', 'autofocus'=>'autofocus']) }}
	    </div>
	</div>
	
	<div class="form-group">
		{{ Form::label('list_grouping', trans('avalon::messages.objects_list_grouping'), ['class'=>'control-label col-sm-2']) }}
	    <div class="col-sm-10">
			{{ Form::text('list_grouping', $object->list_grouping, ['class'=>'form-control', 'data-provide'=>'typeahead', 'data-source'=>$list_groupings]) }}
	    </div>
	</div>
		
	<div class="form-group">
		{{ Form::label('name', trans('avalon::messages.objects_name'), ['class'=>'control-label col-sm-2']) }}
	    <div class="col-sm-10">
			{{ Form::text('name', $object->name, ['class'=>'required form-control']) }}
	    </div>
	</div>
		
	<div class="form-group">
		{{ Form::label('model', trans('avalon::messages.objects_model'), ['class'=>'control-label col-sm-2']) }}
	    <div class="col-sm-10">
			{{ Form::text('model', $object->model, ['class'=>'required form-control']) }}
	    </div>
	</div>
			
	<div class="form-group">
		{{ Form::label('order_by', trans('avalon::messages.objects_order_by'), ['class'=>'control-label col-sm-2']) }}
		<div class="col-sm-10">
			{{ Form::select('order_by', $order_by, $object->order_by, ['class'=>'form-control']) }}
		</div>
	</div>
	
	<div class="form-group">
		{{ Form::label('direction', trans('avalon::messages.objects_direction'), ['class'=>'control-label col-sm-2']) }}
		<div class="col-sm-10">
			{{ Form::select('direction', $direction, $object->direction, ['class'=>'form-control']) }}
		</div>
	</div>
		
	<!-- (not implemented yet)
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<div class="checkbox">
				<label>
					{{ Form::checkbox('singleton', 'on', $object->singleton) }}
					@lang('avalon::messages.objects_singleton')
				</label>
			</div>
		</div>
	</div>
	-->

	@if (count($group_by_field))
	<div class="form-group">
		{{ Form::label('group_by_field', trans('avalon::messages.objects_group_by'), ['class'=>'control-label col-sm-2']) }}
		<div class="col-sm-10">
			{{ Form::select('group_by_field', $group_by_field, $object->group_by_field, ['class'=>'form-control']) }}
		</div>
	</div>
	@endif

	@if (count($related_objects))
	<div class="form-group">
		{{ Form::label('group_by_field', trans('avalon::messages.objects_related'), ['class'=>'control-label col-sm-2']) }}
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
		<div class="col-sm-offset-2 col-sm-10">
			<div class="checkbox">
				<label>
					{{ Form::checkbox('can_create', 'on', $object->can_create) }}
					@lang('avalon::messages.objects_can_create')
				</label>
			</div>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<div class="checkbox">
				<label>
					{{ Form::checkbox('can_edit', 'on', $object->can_edit) }}
					@lang('avalon::messages.objects_can_edit')
				</label>
			</div>
		</div>
	</div>

	<div class="form-group">
		{{ Form::label('list_help', trans('avalon::messages.objects_list_help'), ['class'=>'control-label col-sm-2']) }}
		<div class="col-sm-10">
			{{ Form::textarea('list_help', $object->list_help, ['class'=>'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		{{ Form::label('form_help', trans('avalon::messages.objects_form_help'), ['class'=>'control-label col-sm-2']) }}
		<div class="col-sm-10">
			{{ Form::textarea('form_help', $object->form_help, ['class'=>'form-control']) }}
		</div>
	</div>

	<div class="form-group">
	    <div class="col-sm-10 col-sm-offset-2">
			{{ Form::submit(trans('avalon::messages.site_save'), ['class'=>'btn btn-primary']) }}
			{{ HTML::link(URL::action('InstanceController@index', $object->name), trans('avalon::messages.site_cancel'), ['class'=>'btn btn-default']) }}
	    </div>
	</div>

	{{ Form::close() }}
	
@endsection

@section('side')
	
	<p>@lang('avalon::messages.objects_edit_help', ['title'=>$object->title])</p>

	@if (!$dependencies)
		{{ Form::open(['method'=>'delete', 'action'=>['ObjectController@destroy', $object->name]]) }}
		<button type="submit" class="btn btn-default btn-xs">@lang('avalon::messages.objects_destroy')</button>
		{{ Form::close() }}
	@else
		<p>@lang('avalon::messages.objects_dependencies', ['dependencies', $dependencies])</p>
	@endif

@endsection