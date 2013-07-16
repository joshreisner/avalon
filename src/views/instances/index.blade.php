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
		<a class="btn" href="{{ URL::action('ObjectController@edit', $object->id) }}"><i class="icon-cog"></i> {{ Lang::get('avalon::messages.objects_edit', array('title'=>$object->title)) }}</a>
		<a class="btn" href="{{ URL::action('FieldController@index', $object->id) }}"><i class="icon-list"></i> {{ Lang::get('avalon::messages.fields') }}</a>
		<a class="btn" href="{{ URL::action('InstanceController@create', $object->id) }}"><i class="icon-plus"></i> {{ Lang::get('avalon::messages.instances_create') }}</a>
	</div>


	@if (count($instances))
		<?php
		$table = new Table;
		$table->rows($instances);
		foreach ($fields as $field) $table->column($field->name, $field->type, $field->title);
		$table->column('updated_at', 'updated', Lang::get('avalon::messages.site_updated'));
		$table->deletable();
		if (!empty($object->group_by_field)) $table->groupBy('group');
		if (($object->order_by == 'precedence') && (count($instances) > 1)) $table->draggable(URL::action('InstanceController@reorder', $object->id));
		echo $table->draw();
		?>
	@else
	<div class="alert">
		{{ Lang::get('avalon::messages.instances_empty', array('title'=>$object->title)) }}
	</div>
	@endif
@endsection

@section('side')
	<p>{{ nl2br($object->list_help) }}</p>
@endsection