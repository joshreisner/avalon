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
		Add Field
	</h1>
@endsection

@section('main')
	{{ Form::open(URL::to_route('fields_add'), 'POST', array('class'=>'form-horizontal')) }}
		<div class="control-group">
			<label class="control-label" for="title">Title</label>
			<div class="controls">
				<input type="text" name="title" id="title" class="required" autofocus>
			</div>
		</div>

		<div class="control-group type">
			<label class="control-label" for="title">Type</label>
			<div class="controls">
				<select name="type" id="type">
					<option value="checkbox">Checkbox</option>
					<option value="checkboxes">Checkboxes</option>
					<option value="color">Color</option>
					<option value="date">Date</option>
					<option value="date-time">Date &amp; Time</option>
					<option value="email">Email</option>
					<option value="dropdown">Dropdown</option>
					<option value="file">File</option>
					<option value="file-size">File Size</option>
					<option value="file-type">File Type</option>
					<option value="image">Image</option>
					<option value="image-alt">Image (Alternate)</option>
					<option value="integer">Integer</option>
					<option value="lat-lon">Latitude &amp; Longitude</option>
					<option value="text" selected>Text</option>
					<option value="textarea">Textarea (Rich)</option>
					<option value="textarea-plain">Textarea (Plain)</option>
					<option value="typeahead">Typeahead</option>
					<option value="url">URL</option>
					<option value="url-slug">URL (Local)</option>
				</select>
			</div>
		</div>

		<div class="control-group type">
			<label class="control-label" for="visibility">Visibility</label>
			<div class="controls">
				<select name="type" name="visibility" id="type">
					<option value="list">Show in List</option>
					<option value="normal" selected>Normal</option>
					<option value="hidden">Hidden</option>
				</select>
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

		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Save changes</button>
			<a class="btn" href="{{ URL::to_route('fields', $object->id) }}">Cancel</a>
		</div>		
	</form>
@endsection