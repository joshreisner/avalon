@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.fields') }}
@endsection

@section('main')

	{{ Breadcrumbs::leave(array(
		URL::action('ObjectController@index')=>Lang::get('avalon::messages.objects'),
		URL::action('InstanceController@index', $object->id)=>$object->title,
		Lang::get('avalon::messages.fields'),
		)) }}

	<div class="btn-group">
		<a class="btn btn-default" href="{{ URL::action('FieldController@create', $object->id) }}"><i class="icon-plus"></i> {{ Lang::get('avalon::messages.fields_create') }}</a>
	</div>

	@if (count($fields))
		{{ Table::rows($fields)
			->draggable(URL::action('FieldController@reorder', $object->id))
			->column('title', 'string', Lang::get('avalon::messages.fields_title'))
			->column('type', 'string', Lang::get('avalon::messages.fields_type'))
			->column('updated_at', 'updated_at', Lang::get('avalon::messages.site_updated_at'))
			->draw('fields')
			}}
	@else
	<div class="alert alert-warning">
		{{ Lang::get('avalon::messages.fields_empty') }}
	</div>
	@endif

@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.fields_list_help', array('title'=>$object->title)) }}</p>
@endsection