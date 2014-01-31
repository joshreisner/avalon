<ul>
@foreach ($instances as $instance)
	<li id="item-{{ $instance->id }}">
		<div class="nested_row @if ($instance->deleted_at) inactive@endif">
			<div class="draggy"><i class="glyphicon glyphicon-align-justify"></i></div>
			<a href="{{  URL::action('InstanceController@edit', array($object->id, $instance->id)) }}">{{ $instance->title }}</a>
			<div class="updated_at">{{ Dates::relative($instance->updated_at) }}</div>
			<div class="delete">
				<a href="{{ $instance->delete }}">
				@if ($instance->deleted_at)
					<i class="glyphicon glyphicon-remove-circle"></i>
				@else
					<i class="glyphicon glyphicon-ok-circle"></i>
				@endif
				</a></div>
		</div>
		@if (count($instance->children))
			@include('avalon::instances.nested', array('instances'=>$instance->children))
		@endif
	</li>
@endforeach
</ul>