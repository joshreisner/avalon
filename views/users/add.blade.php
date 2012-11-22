@layout('avalon::template')

@section('breadcrumbs')
	<h1>
		<a href="/"><i class="icon-home"></i></a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		<a href="{{ URL::to_route('objects') }}">Objects</a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		<a href="{{ URL::to_route('users') }}">Users</a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		{{ $title }}
	</h1>
@endsection

@section('main')
	{{ Form::open(URL::to_route('users_add'), 'POST', array('class'=>'form-horizontal')) }}
		<div class="control-group">
			<label class="control-label" for="firstname">First Name</label>
			<div class="controls">
				<input type="text" name="firstname" class="required" autofocus>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="lastname">Last Name</label>
			<div class="controls">
				<input type="text" name="lastname" class="required">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="email">Email</label>
			<div class="controls">
				<input type="text" name="email" class="required email">
			</div>
		</div>
		
		<div class="control-group role">
			<label class="control-label" for="email">Role</label>
			<div class="controls">
				<div class="btn-group" data-toggle="buttons-radio" data-toggle-name="role">
					@foreach ($roles as $key=>$value)
					<button type="button"  value="{{ $key }}" class="btn@if (3 == $key) active@endif">{{ $value }}</button>
					@endforeach
				</div>
				<input type="hidden" name="role" value="3">
			</div>
		</div>
		
		<div class="control-group permissions">
			<label class="control-label" for="permissions">Permissions</label>
			<div class="controls">
				<div class="btn-group" data-toggle="buttons-radio" data-toggle-name="role">
					@foreach ($objects as $object)
					<label class="checkbox inline">
						<input type="checkbox" name="permissions_{{ $object->id }}" checked> {{ $object->title }}
					</label>
					@endforeach
				</div>
			</div>
		</div>
		
		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Save changes</button>
			<a class="btn" href="{{ URL::to_route('users') }}">Cancel</a>
		</div>		
	</form>
@endsection