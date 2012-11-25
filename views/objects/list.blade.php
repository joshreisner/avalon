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
		@if ($user->role < 3)
		<a class="btn" href="{{ URL::to_route('settings') }}"><i class="icon-cogs"></i> Site Settings</a>
		<a class="btn" href="{{ URL::to_route('users') }}"><i class="icon-group"></i> Users</a>
		@endif
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
					<th class="integer"># Active</th>
					<th class="date">Last Update</th>
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
			   		<td class="integer">{{ $object->count }}</td>
			   		<td class="date">@if (!empty($object->updated_by))<span class="user">{{ $object->updated_by }}</span>@endif{{ $object->updated_at }}</td>
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
