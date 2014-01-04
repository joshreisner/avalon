@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.objects_create') }}
@endsection

@section('main')

	{{ Breadcrumbs::leave(array(
		URL::action('ObjectController@index')=>Lang::get('avalon::messages.objects'),
		Lang::get('avalon::messages.objects_create'),
		)) }}

	{{ Form::open(array('class'=>'form-horizontal', 'url'=>URL::action('ObjectController@store'))) }}

	<div class="form-group">
		{{ Form::label('title', Lang::get('avalon::messages.objects_title'), array('class'=>'col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::text('title', '', array('class'=>'required form-control', 'autofocus'=>'autofocus')) }}
	    </div>
	</div>

	<div class="form-group">
		{{ Form::label('list_grouping', Lang::get('avalon::messages.objects_list_grouping'), array('class'=>'col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::text('list_grouping', '', array('class'=>'form-control')) }}
	    </div>
	</div>

	<div class="form-group">
		{{ Form::label('order_by', Lang::get('avalon::messages.objects_order_by'), array('class'=>'col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::select('order_by', $order_by, 'precedence', array('class'=>'form-control')) }}
	    </div>
	</div>

	<div class="form-group">
		{{ Form::label('direction', Lang::get('avalon::messages.objects_direction'), array('class'=>'col-sm-2')) }}
	    <div class="col-sm-10">
			{{ Form::select('direction', $direction, 'asc', array('class'=>'form-control')) }}
	    </div>
	</div>

	<div class="form-group">
	    <div class="col-sm-10 col-sm-offset-2">
			{{ Form::submit(Lang::get('avalon::messages.site_save'), array('class'=>'btn btn-primary')) }}
			{{ HTML::link(URL::action('ObjectController@index'), Lang::get('avalon::messages.site_cancel'), array('class'=>'btn btn-default')) }}
	    </div>
	</div>

	{{ Form::close() }}
		
@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.objects_create_help') }}</p>
@endsection