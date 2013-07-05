@extends('avalon::template');

@section('title')
	{{ Lang::get('avalon::messages.objects_create') }}
@endsection

@section('main')
	<h1 class="breadcrumbs">
		<a href="/"><i class="icon-home"></i></a>
		<i class="icon-chevron-right"></i>
		<a href="/login/objects">{{ Lang::get('avalon::messages.objects') }}</a>
		<i class="icon-chevron-right"></i>
		{{ Lang::get('avalon::messages.objects_create') }}
	</h1>
	
	<div class="btn-group">
		<a class="btn" href="/login/settings"><i class="icon-cog"></i> {{ Lang::get('avalon::messages.site_settings') }}</a>
		<a class="btn" href="/login/users"><i class="icon-group"></i> {{ Lang::get('avalon::messages.users') }}</a>
		<a class="btn active" href="/login/objects/add"><i class="icon-plus"></i> {{ Lang::get('avalon::messages.objects_create') }}</a>
	</div>

	<form class="form-horizontal" method="post" action="/login/objects/create">
		<div class="control-group">
			<label class="control-label" for="email">{{ Lang::get('avalon::messages.objects_title') }}</label>
	    	<div class="controls">
	    		<input type="text" name="title" class="required title" autofocus="autofocus">
	    	</div>
		</div>
		<div class="control-group">
	    	<div class="controls">
	    		<input type="submit" class="btn" value="{{ Lang::get('avalon::messages.objects_create') }}">
	    	</div>
		</div>
	</form>
@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.objects_create_help') }}</p>
@endsection