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
	{{ Form::open(URL::to_route('instances_edit', array($object->id, $instance->id)), 'PUT', array('class'=>'form-horizontal')) }}

	@foreach ($object->fields as $field)
		<div class="control-group {{ $field->field_name }}">
			<label class="control-label" for="{{ $field->field_name }}">{{ $field->title }}</label>

			@if (($user->role == 1) && ($field->type == 'textarea-rich'))
			<a class="help" href="#lorem-ipsum">Lorem Ipsum</a>
			@endif
			
			<div class="controls">
				@if ($field->type == 'checkbox')
				<label class="checkbox inline">
					<input type="checkbox" name="{{ $field->field_name }}" id="{{ $field->field_name }}"@if ($instance->{$field->field_name}) checked@endif>
				</label>				
				
				@elseif ($field->type == 'color')
				<input type="text" name="{{ $field->field_name }}" id="{{ $field->field_name }}" class="span2 color {hash:true@if (!$field->required), required:false@endif}" value="{{ $instance->{$field->field_name} }}">
				
				@elseif ($field->type == 'date')
				<input type="date" name="{{ $field->field_name }}" id="{{ $field->field_name }}" class="span2@if ($field->required) required@endif" value="{{ $instance->{$field->field_name} }}">
				
				@elseif ($field->type == 'email')
				<input type="text" name="{{ $field->field_name }}" id="{{ $field->field_name }}" class="span3 email@if ($field->required) required@endif" value="{{ $instance->{$field->field_name} }}">

				@elseif ($field->type == 'integer')
				<input type="text" name="{{ $field->field_name }}" id="{{ $field->field_name }}" class="span2 integer@if ($field->required) required@endif" value="{{ $instance->{$field->field_name} }}">

				@elseif ($field->type == 'text')
				<input type="text" name="{{ $field->field_name }}" id="{{ $field->field_name }}" class="span5@if ($field->required) required@endif" value="{{ $instance->{$field->field_name} }}">
				
				@elseif ($field->type == 'textarea-plain')
				<textarea name="{{ $field->field_name }}" id="{{ $field->field_name }}" class="span6@if ($field->required) required@endif">{{ $instance->{$field->field_name} }}</textarea>
				
				@elseif ($field->type == 'textarea-rich')
				<textarea name="{{ $field->field_name }}" id="{{ $field->field_name }}" class="textarea_rich@if ($field->required) required@endif">{{ $instance->{$field->field_name} }}</textarea>

				@elseif ($field->type == 'typeahead')
				<input type="text" name="{{ $field->field_name }}" id="{{ $field->field_name }}" class="span5@if ($field->required) required@endif" value="{{ $instance->{$field->field_name} }}" data-provide="typeahead" data-source="{{ $field->values }}">
				
				@elseif ($field->type == 'url')
				<input type="text" name="{{ $field->field_name }}" id="{{ $field->field_name }}" class="span4 url@if ($field->required) required@endif" value="{{ $instance->{$field->field_name} }}">

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