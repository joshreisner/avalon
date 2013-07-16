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

	{{ Former::horizontal_open()->action(URL::action('ObjectController@update', $object->id))->method('put') }}
	
	{{ Former::text('title')
		->label(Lang::get('avalon::messages.objects_title'))
		->value($object->title)
		->class('required')
		->inlineHelp(Lang::get('avalon::messages.objects_title_help'))
		}}
	
	{{ Former::text('list_grouping')
		->label(Lang::get('avalon::messages.objects_list_grouping'))
		->value($object->list_grouping)
		->data_provide('typeahead')
		->data_source($typeahead)
		}}
	
	{{ Former::text('name')
		->label(Lang::get('avalon::messages.objects_name'))
		->value($object->name)
		->class('required')
		}}
	
	<!-- doesn't work yet
	{{ Former::select('order_by')
		->options($order_by)
		->label(Lang::get('avalon::messages.objects_order_by'))
		->value($object->order_by)
		}} -->

	<div class="control-group">
		<label for="title" class="control-label">{{ Lang::get('avalon::messages.objects_order_by') }}</label>
		<div class="controls">
			{{ Form::select('order_by', $order_by, $object->order_by) }}			
		</div>
	</div>
	
	{{ Former::select('direction')
		->options($direction)
		->label(Lang::get('avalon::messages.objects_direction'))
		->value($object->direction)
		}}
	
	@if (count($group_by_field))
	{{ Former::select('group_by_field')
		->options(array(''=>''))
		->fromQuery($group_by_field, 'title')
		->value($object->group_by_field)
		->label(Lang::get('avalon::messages.objects_group_by'))
		}}
	@endif
	
	{{ Former::textarea('list_help')
		->label(Lang::get('avalon::messages.objects_list_help'))
		->value($object->list_help)
		}}
	
	{{ Former::textarea('form_help')
		->label(Lang::get('avalon::messages.objects_form_help'))
		->value($object->form_help)
		}}
	
	{{ Former::actions()
		->primary_submit(Lang::get('avalon::messages.site_save'))
		->link(Lang::get('avalon::messages.site_cancel'), URL::action('InstanceController@index', $object->id))
		}}
	
	{{ Former::close() }}
	
@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.objects_edit_help', array('title'=>$object->title)) }}</p>

	@if (!$dependencies)
		{{ Form::open(array('method'=>'delete', 'action'=>array('ObjectController@destroy', $object->id))) }}
		<button type="submit" class="btn btn-mini">{{ Lang::get('avalon::messages.objects_destroy') }}</button>
		{{ Form::close() }}
	@else
		<p>{{ Lang::get('avalon::messages.objects_dependencies', array('dependencies', $dependencies)) }}</p>
	@endif

@endsection