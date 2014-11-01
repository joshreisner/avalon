@extends('avalon::template')

@section('title')
	@lang('avalon::messages.fields')
@endsection

@section('main')

	{{ Breadcrumbs::leave([
		URL::action('ObjectController@index')=>trans('avalon::messages.objects'),
		URL::action('InstanceController@index', $object->name)=>$object->title,
		trans('avalon::messages.fields'),
		]) }}

	<div class="btn-group">
		<a class="btn btn-default" id="create" href="{{ URL::action('FieldController@create', $object->name) }}"><i class="glyphicon glyphicon-plus"></i> {{ trans('avalon::messages.fields_create') }}</a>
	</div>

	@if (count($fields))
		{{ Table::rows($fields)
			->draggable(URL::action('FieldController@reorder', $object->name))
			->column('title', 'string', trans('avalon::messages.fields_title'))
			->column('type', 'string', trans('avalon::messages.fields_type'))
			->column('updated_at', 'updated_at', trans('avalon::messages.site_updated_at'))
			->draw('fields')
			}}
	@else
	<div class="alert alert-warning">
		@lang('avalon::messages.fields_empty')
	</div>
	@endif

@endsection

@section('side')
	<p>@lang('avalon::messages.fields_list_help', ['title'=>$object->title])</p>
@endsection

@section('script')
	<script>
	$(document).keypress(function(e){
		if (e.which == 99) {
			location.href = $("a#create").addClass("active").attr("href");
		}
	});

	@if (Session::has('field_id'))
		var $el = $("table tr#{{ Session::get('field_id') }}");
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