@layout('avalon::template')

@section('breadcrumbs')
	<h1>
		<a href="/"><i class="icon-home"></i></a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		<a href="{{ URL::to_route('objects') }}">Objects</a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		<a href="{{ URL::to_route('instances', $object->id) }}">{{ $object->title }}</a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		<a href="{{ URL::to_route('fields', $object->id) }}">Fields</a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		{{ $title }}
	</h1>
@endsection

@section('main')
	{{ Form::open(URL::to_route('fields_add', $object->id), 'POST', array('class'=>'form-horizontal')) }}
		<div class="control-group">
			<label class="control-label" for="title">Title</label>
			<div class="controls">
				<input type="text" name="title" id="title" class="span5 required" autofocus>
			</div>
		</div>

		<div class="control-group type">
			<label class="control-label" for="title">Type</label>
			<div class="controls">
				{{ Form::select('type', $field_types, 'text'); }}
			</div>
		</div>

		<div class="control-group type">
			<label class="control-label" for="visibility">Visibility</label>
			<div class="controls">
				{{ Form::select('visibility', $field_visibilities, 'normal') }}
			</div>
		</div>

		<div class="control-group type">
			<label class="control-label" for="required">Required</label>
			<div class="controls">
				<label class="checkbox inline">
					<input type="checkbox" name="required" id="required">
				</label>
			</div>
		</div>

		<div class="control-group type">
			<label class="control-label" for="additional">Additonal Instructions</label>
			<div class="controls">
				<textarea name="additional" class="span5" id="additional"></textarea>
			</div>
		</div>

		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Save changes</button>
			<a class="btn" href="{{ URL::to_route('fields', $object->id) }}">Cancel</a>
		</div>		
	</form>
@endsection