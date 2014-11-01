@extends('avalon::template')

@section('title')
	@lang('avalon::messages.users')
@endsection

@section('main')

	{{ Breadcrumbs::leave([
		URL::action('ObjectController@index')=>trans('avalon::messages.objects'),
		trans('avalon::messages.users'),
		]) }}

	<div class="btn-group">
		<a class="btn btn-default" id="create" href="{{ URL::action('UserController@create') }}">
			<i class="glyphicon glyphicon-plus"></i> 
			@lang('avalon::messages.users_create')
		</a>
	</div>

	{{ Table::rows($users)
		->column('name', 'string', trans('avalon::messages.users_name'))
		->column('role', 'string', trans('avalon::messages.users_role'))
		->column('last_login', 'date-relative', trans('avalon::messages.users_last_login'))
		->deletable()
		->draw()
		}}

@endsection

@section('side')
	<p>@lang('avalon::messages.users_help')</p>
@endsection

@section('script')
	<script>
	$(document).keypress(function(e){
		if (e.which == 99) {
			location.href = $("a#create").addClass("active").attr("href");
		}
	});

	@if (Session::has('user_id'))
		var $el = $("table tr#{{ Session::get('user_id') }}");
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