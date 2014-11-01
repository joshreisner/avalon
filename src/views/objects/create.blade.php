@extends('avalon::template')

@section('title')
	@lang('avalon::messages.objects_create')
@endsection

@section('main')

	{{ Breadcrumbs::leave([
		URL::action('ObjectController@index')=>trans('avalon::messages.objects'),
		trans('avalon::messages.objects_create'),
		]) }}

	{{ Form::open(['class'=>'form-horizontal', 'url'=>URL::action('ObjectController@store')]) }}

	<div class="form-group">
		{{ Form::label('title', trans('avalon::messages.objects_title'), ['class'=>'control-label col-sm-2']) }}
	    <div class="col-sm-10">
			{{ Form::text('title', false, ['class'=>'required form-control', 'autofocus'=>'autofocus']) }}
	    </div>
	</div>

	<div class="form-group">
		{{ Form::label('list_grouping', trans('avalon::messages.objects_list_grouping'), ['class'=>'control-label col-sm-2']) }}
	    <div class="col-sm-10">
			{{ Form::text('list_grouping', false, ['class'=>'form-control', 'data-provide'=>'typeahead', 'data-source'=>$list_groupings]) }}
	    </div>
	</div>

	<div class="form-group">
		{{ Form::label('order_by', trans('avalon::messages.objects_order_by'), ['class'=>'control-label col-sm-2']) }}
	    <div class="col-sm-10">
			{{ Form::select('order_by', $order_by, 'precedence', ['class'=>'form-control']) }}
	    </div>
	</div>

	<div class="form-group">
		{{ Form::label('direction', trans('avalon::messages.objects_direction'), ['class'=>'control-label col-sm-2']) }}
	    <div class="col-sm-10">
			{{ Form::select('direction', $direction, 'asc', ['class'=>'form-control']) }}
	    </div>
	</div>

	<div class="form-group">
	    <div class="col-sm-10 col-sm-offset-2">
			{{ Form::submit(trans('avalon::messages.site_save'), ['class'=>'btn btn-primary']) }}
			{{ HTML::link(URL::action('ObjectController@index'), trans('avalon::messages.site_cancel'), ['class'=>'btn btn-default']) }}
	    </div>
	</div>

	{{ Form::close() }}
		
@endsection

@section('side')
	<p>@lang('avalon::messages.objects_create_help')</p>
@endsection