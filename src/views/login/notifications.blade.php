@if (Session::has('error'))
	<div class="alert alert-danger">
		{{ Session::get('error') }}
	</div>
@elseif (Session::has('message'))
	<div class="alert alert-warning">
		{{ Session::get('message') }}
	</div>
@endif