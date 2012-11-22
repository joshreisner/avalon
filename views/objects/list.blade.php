@layout('avalon::template')

@section('breadcrumbs')
	<h1>
		<a href="/"><i class="icon-home"></i></a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		{{ $title }}
	</h1>
@endsection

@section('buttons')
	<nav class="btn-group">
		<a class="btn" href="{{ URL::to_route('settings') }}"><i class="icon-cogs"></i> Site Settings</a>
		<a class="btn" href="{{ URL::to_route('users') }}"><i class="icon-group"></i> Users</a>
		<a class="btn" href="{{ URL::to_route('objects_add') }}"><i class="icon-pencil"></i> Add Object</a>
	</nav>
@endsection

@section('main')
	@if (count($objects) > 0)
		<?php $lastgroup = '';?>
		<table class="table table-condensed">
			<thead>
				<tr>
					<th>Object</th>
					<th># Active</th>
					<th class="span2 date">Last Update</th>
				</tr>
			</thead>
			<tbody>
		@foreach ($objects as $object)
			@if ($lastgroup != $object->list_grouping)
				<?php $lastgroup = $object->list_grouping;?>
				<tr>
					<td colspan="3" class="group">{{ $lastgroup }}</td>
				</tr>
			@endif
			   	<tr>
			   		<td><a href="{{ URL::to_route('instances', $object->id) }}">{{ $object->title }}</a></td>
			   		<td>234</td>
			   		<td class="date"><span class="user">Josh</span>Jan 14, 2012</td>
			   	</tr>
		@endforeach
			</tbody>
		</table>
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
