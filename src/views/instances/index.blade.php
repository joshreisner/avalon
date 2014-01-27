@extends('avalon::template')

@section('title')
	{{ $object->title }}
@endsection

@section('main')

	{{ Breadcrumbs::leave(array(
		URL::action('ObjectController@index')=>Lang::get('avalon::messages.objects'),
		$object->title,
		)) }}

	<div class="btn-group">
		<a class="btn btn-default" href="{{ URL::action('ObjectController@edit', $object->id) }}"><i class="glyphicon glyphicon-cog"></i> {{ Lang::get('avalon::messages.objects_edit', array('title'=>$object->title)) }}</a>
		<a class="btn btn-default" href="{{ URL::action('FieldController@index', $object->id) }}"><i class="glyphicon glyphicon-list"></i> {{ Lang::get('avalon::messages.fields') }}</a>
		<a class="btn btn-default" href="{{ URL::action('InstanceController@create', $object->id) }}"><i class="glyphicon glyphicon-plus"></i> {{ Lang::get('avalon::messages.instances_create') }}</a>
	</div>

	@if (count($instances))
		<?php
		$table = new Table;
		$table->rows($instances);
		foreach ($fields as $field) $table->column($field->name, $field->type, $field->title);
		$table->column('updated_at', 'updated_at', Lang::get('avalon::messages.site_updated_at'));
		$table->deletable();
		if (!empty($object->group_by_field)) $table->groupBy('group');
		if ($object->order_by == 'precedence') $table->draggable(URL::action('InstanceController@reorder', $object->id));
		echo $table->draw();
		?>
	@else
	<div class="alert alert-warning">
		{{ Lang::get('avalon::messages.instances_empty', array('title'=>$object->title)) }}
	</div>
	@endif

@endsection

@section('side')
	<p>{{ nl2br($object->list_help) }}</p>
@endsection