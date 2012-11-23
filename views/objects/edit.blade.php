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
	{{ Form::open(URL::to_route('objects_edit', $object->id), 'PUT', array('class'=>'form-horizontal')) }}
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