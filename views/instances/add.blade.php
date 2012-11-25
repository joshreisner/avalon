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
	{{ Form::open(URL::to_route('instances_add', $object->id), 'POST', array('class'=>'form-horizontal')) }}

	@foreach ($object->fields as $field)
		<div class="control-group {{ $field->field_name }}">
			<label class="control-label" for="{{ $field->field_name }}">{{ $field->title }}</label>
			<div class="controls">
				@if ($field->type == 'text')
				<input type="text" name="{{ $field->field_name }}" id="{{ $field->field_name }}" class="span5@if ($field->required) required@endif">
				@elseif ($field->type == 'textarea-plain')
				<textarea name="{{ $field->field_name }}" id="{{ $field->field_name }}" class="span5@if ($field->required) required@endif"></textarea>
				@else
				<div class="alert error">field type of {{ $field->type }} not handled!</div>
				@endif
				@if (!empty($field->additional))
				<span class="help-inline">{{ $field->additional }}</span>
				@endif
			</div>
		</div>
	@endforeach

		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Save changes</button>
			<a class="btn" href="{{ URL::to_route('instances', $object->id) }}">Cancel</a>
		</div>		
	</form>
@endsection

@section('side')
	<div class="inner">
		{{ nl2br($object->form_help) }}
	</div>
@endsection