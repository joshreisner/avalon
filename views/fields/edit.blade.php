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
	{{ Form::open(URL::to_route('fields_edit', array($object->id, $field->id)), 'PUT', array('class'=>'form-horizontal')) }}
		<div class="control-group">
			<label class="control-label" for="title">Title</label>
			<div class="controls">
				<input type="text" name="title" id="title" class="span5 required" value="{{ $field->title }}" autofocus>
			</div>
		</div>

		<div class="control-group type">
			<label class="control-label" for="title">Type</label>
			<div class="controls">
				<select name="type" id="type">
					@foreach ($field_types as $f)
					<option value="{{ Str::slug($f) }}"@if ($field->type == Str::slug($f)) selected@endif>{{ $f }}</option>
					@endforeach
				</select>
			</div>
		</div>

		<div class="control-group type">
			<label class="control-label" for="visibility">Visibility</label>
			<div class="controls">
				<select name="visibility" id="visibility">
					<option value="list"@if ($field->visibility == 'list') selected@endif>Show in List</option>
					<option value="normal"@if ($field->visibility == 'normal') selected@endif>Normal</option>
					<option value="hidden"@if ($field->visibility == 'hidden') selected@endif>Hidden</option>
				</select>
			</div>
		</div>

		<div class="control-group type">
			<label class="control-label" for="required">Required</label>
			<div class="controls">
				<label class="checkbox inline">
					<input type="checkbox" name="required" id="required"@if ($field->required) checked@endif>
				</label>
			</div>
		</div>

		<div class="control-group type">
			<label class="control-label" for="additional">Additonal Instructions</label>
			<div class="controls">
				<textarea name="additional" class="span5" id="additional">{{ $field->additional }}</textarea>
			</div>
		</div>

		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Save changes</button>
			<a class="btn" href="{{ URL::to_route('fields', $object->id) }}">Cancel</a>
		</div>		
	</form>
@endsection