@layout('avalon::template')

@section('breadcrumbs')
	<h1>
		<a href="/"><i class="icon-home"></i></a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		<a href="{{ URL::to_route('objects') }}">Objects</a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		<a href="{{ URL::to_route('instances', $object->id) }}">{{ $object->title }}</a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		{{ $title }}
	</h1>
@endsection

@section('main')
	{{ Form::open(URL::to_route('objects_edit', $object->id), 'PUT', array('class'=>'form-horizontal objects_edit')) }}
		<div class="control-group">
			<label class="control-label" for="title">Title</label>
			<div class="controls">
				<input type="text" name="title" class="span5 required" value="{{ $object->title }}" autofocus>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="list_grouping">List Grouping</label>
			<div class="controls">
				<input type="text" name="list_grouping" class="span5" value="{{ $object->list_grouping }}" data-provide="typeahead" data-source="{{ $list_groupings }}">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="table_name">Table Name</label>
			<div class="controls">
				<input type="text" name="table_name" class="span5 required" value="{{ $object->table_name }}">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="order_by">Order By</label>
			<div class="controls">
				{{ Form::select('order_by', array('created_at'=>'Date Created', 'updated_at'=>'Date Updated', 'precedence'=>'Precedence'), $object->order_by) }}
				{{ Form::select('direction', array('ASC'=>'Ascending', 'DESC'=>'Descending'), $object->direction) }}
			</div>
		</div>

		@if ($group_by_fields)
		<div class="control-group">
			<label class="control-label" for="order_by">Group By</label>
			<div class="controls">
				{{ Form::select('group_by_field', $group_by_fields, $object->group_by_field) }}
			</div>
		</div>
		@endif

		<div class="control-group type">
			<label class="control-label" for="show_published">Show Published</label>
			<div class="controls">
				<label class="checkbox inline">
					{{ Form::checkbox('show_published', 'on', $object->show_published) }}
				</label>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="list_help">List Help</label>
			<div class="controls">
				<textarea name="list_help" class="span5">{{ $object->list_help }}</textarea>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="form_help">Form Help</label>
			<div class="controls">
				<textarea name="form_help" class="span5">{{ $object->form_help }}</textarea>
			</div>
		</div>

		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Save changes</button>
			<a class="btn" href="{{ URL::to_route('instances', $object->id) }}">Cancel</a>
		</div>
	</form>
@endsection

@section('side')
	<div class="inner">
		<p>You can drop this object and all of its associated fields and instances.</p>
		<p>You can also resize all the images.</p>
		<p>Or you can expunge the N values in this object.</p>
		<p>You can also duplicate this object and all of its values.</p>
		<p>You can also refresh the search indexes for all this object's values.</p>
	</div>
@endsection