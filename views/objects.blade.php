@layout('avalon::page')

@section('breadcrumbs')
	<h1>
		<a href="/"><i class="icon-home"></i></a><span class="separator"><i class="icon-chevron-right"></i></span>Objects
	</h1>
@endsection

@section('main')

	@if (count($objects) > 0)
		@foreach ($comments as $comment)
		    
		@endforeach	    
	@else
		<div class="alert">No objects have been entered yet.</div>
	@endif

@endsection

@section('side')
	<div class="inner">
		<p>This is the main directory of website ‘objects.’ Those that are linked you have permission to edit.</p>
		<p>You're logged in as {{ $user->firstname }}.<br>Click {{ HTML::link_to_route('logout', 'here') }} to log out.</p>
	</div>
@endsection
