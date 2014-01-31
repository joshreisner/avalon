<ul>
@foreach ($instances as $instance)
	<li id="item-{{ $instance->id }}">
		<div class="nested_row">
			<div class="draggy"><i class="glyphicon glyphicon-align-justify"></i></div>
			<a href="{{  URL::action('InstanceController@edit', array($object->id, $instance->id)) }}">{{ $instance->title }}</a>
			<time>{{ Dates::relative($instance->updated_at) }}</time>
			<div class="delete"><a href="{{ $instance->delete }}"><i class="glyphicon glyphicon-ok-circle"></i></a></div>
		</div>
		@if (count($instance->children))
			@include('avalon::instances.nested', array('instances'=>$instance->children))
		@endif
	</li>
@endforeach
</ul>