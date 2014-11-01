@extends('avalon::template')

@section('title')
	{{ $object->title }}
@endsection

@section('main')

	{{ Breadcrumbs::leave([
		URL::action('ObjectController@index')=>trans('avalon::messages.objects'),
		$object->title,
		]) }}

	<div class="btn-group">
		@if (Auth::user()->role < 2)
		<a class="btn btn-default" href="{{ URL::action('ObjectController@edit', $object->name) }}">
			<i class="glyphicon glyphicon-cog"></i> 
			@lang('avalon::messages.objects_edit', ['title'=>$object->title])
		</a>
		<a class="btn btn-default" href="{{ URL::action('FieldController@index', $object->name) }}">
			<i class="glyphicon glyphicon-list"></i>
			@lang('avalon::messages.fields')
		</a>
		@endif
		@if ($object->can_create)
			<a class="btn btn-default" id="create" href="{{ URL::action('InstanceController@create', $object->name) }}">
				<i class="glyphicon glyphicon-plus"></i>
				@lang('avalon::messages.instances_create')
			</a>
		@endif
	</div>

	@if (count($instances))
		@if ($object->nested)
			<div class="nested" data-draggable-url="{{ URL::action('InstanceController@reorder', $object->name) }}">
				<div class="legend">
					Title
					<div class="updated_at">Updated</div>
				</div>
				@include('avalon::instances.nested', ['instances'=>$instances])
			</div>
		@else
			{{ InstanceController::table($object, $fields, $instances) }}
		@endif
	@else
	<div class="alert alert-warning">
		@lang('avalon::messages.instances_empty', ['title'=>strtolower($object->title)])
	</div>
	@endif
		
@endsection

@section('side')
	<p>{{ nl2br($object->list_help) }}</p>
@endsection

@section('script')
	<script>
	$(document).keypress(function(e){
		if (e.which == 99) {
			location.href = $("a#create").addClass("active").attr("href");
		}
	});

	@if (Session::has('instance_id'))
		var $el = $("table tr#{{ Session::get('instance_id') }}");
		$el
			.after("<div class='highlight'/>")
			.next()
            .width($el.width())
            .height($el.height())
            .css("marginTop", -$el.height())
			.fadeOut(500, function(){
				$("div.highlight").remove();
			});
	@endif
	</script>
@endsection