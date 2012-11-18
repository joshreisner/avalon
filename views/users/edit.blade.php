@layout('avalon::template')

@section('breadcrumbs')
	<h1>
		<a href="/"><i class="icon-home"></i></a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		<a href="{{ URL::to_route('objects') }}">Objects</a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		<a href="{{ URL::to_route('users') }}">Users</a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		Edit User
	</h1>
@endsection

@section('main')
	{{ Form::open('login/users/' . $user->id, 'PUT', array('class'=>'form-horizontal')) }}
		<div class="control-group">
			<label class="control-label" for="firstname">First Name</label>
			<div class="controls">
				<input type="text" name="firstname" value="{{ $user->firstname }}" class="required">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="lastname">Last Name</label>
			<div class="controls">
				<input type="text" name="lastname" value="{{ $user->lastname }}" class="required">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="email">Email</label>
			<div class="controls">
				<input type="text" name="email" value="{{ $user->email }}" class="required email">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="password">Password</label>
			<div class="controls">
				<a class="btn" href="/login/users">Reset</a>
			</div>
		</div>
		
		<div class="control-group role">
			<label class="control-label" for="role">Role</label>
			<div class="controls">
				<div class="btn-group" data-toggle="buttons-radio" data-toggle-name="role">
					@foreach ($roles as $key=>$value)
					<button type="button"  value="{{ $key }}" class="btn@if ($user->role == $key) active@endif">{{ $value }}</button>
					@endforeach
				</div>
				<input type="hidden" name="role" value="{{ $user->role }}">
			</div>
		</div>
		<div class="control-group permissions@if ($user->role != 3) hidden@endif">
			<label class="control-label" for="permissions">Permissions</label>
			<div class="controls">
				<div class="btn-group" data-toggle="buttons-radio" data-toggle-name="role">
					@foreach ($objects as $object)
					<label class="checkbox inline">
						<input type="checkbox" name="permissions_{{ $object->id }}"@if (in_array($object->id, $permissions)) checked@endif> {{ $object->title }}
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