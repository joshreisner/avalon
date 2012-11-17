@layout('avalon::page')

@section('breadcrumbs')
	<h1>
		<a href="/"><i class="icon-home"></i></a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		<a href="{{ URL::to_route('objects') }}">Objects</a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		Edit User
	</h1>
@endsection

@section('main')
	{{ Form::open('login/users/' . $user->id, 'PUT', array('class'=>'form-horizontal')) }}
	<!-- <form class="form-horizontal" method="post" action="{{ URL::current() }}"> -->
		<div class="control-group">
			<label class="control-label" for="firstname">First Name</label>
			<div class="controls">
				<input type="text" name="firstname" value="{{ $user->firstname }}">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="lastname">Last Name</label>
			<div class="controls">
				<input type="text" name="lastname" value="{{ $user->lastname }}">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="email">Email</label>
			<div class="controls">
				<input type="text" name="email" value="{{ $user->email }}">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="email">Password</label>
			<div class="controls">
				<a class="btn" href="/login/users">Reset</a>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="email">Role</label>
			<div class="controls">
				<div class="btn-group" data-toggle="buttons-radio" data-toggle-name="role">
					@foreach ($roles as $key=>$value)
					<button type="button"  value="{{ $key }}" class="btn@if ($user->role == $key) active@endif">{{ $value }}</button>
					@endforeach
				</div>
				<input type="hidden" name="role" value="{{ $user->role }}">
			</div>
		</div>
		
		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Save changes</button>
			<a class="btn" href="/login/users">Cancel</a>
		</div>		
	</form>
@endsection