@layout('avalon::template')

@section('breadcrumbs')
	<h1>
		<a href="/"><i class="icon-home"></i></a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		<a href="{{ URL::to_route('objects') }}">Objects</a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		Add Object
	</h1>
@endsection

@section('main')
	{{ Form::open(URL::to_route('objects_add'), 'POST', array('class'=>'form-horizontal')) }}
		<div class="control-group">
			<label class="control-label" for="title">Title</label>
			<div class="controls">
				<input type="text" name="title" class="required" autofocus>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="list_grouping">List Grouping</label>
			<div class="controls">
				<input type="text" name="list_grouping">
			</div>
		</div>

		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Save changes</button>
			<a class="btn" href="/login/users">Cancel</a>
		</div>		
	</form>
@endsection