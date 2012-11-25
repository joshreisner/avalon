@layout('avalon::template')

@section('breadcrumbs')
	<h1>
		<a href="/"><i class="icon-home"></i></a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		<a href="{{ URL::to_route('objects') }}">Objects</a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		{{ $title }}
	</h1>
@endsection

@section('main')
	{{ Form::open(URL::to_route('objects_add'), 'POST', array('class'=>'form-horizontal objects_add')) }}
		<div class="control-group">
			<label class="control-label" for="title">Title</label>
			<div class="controls">
				<input type="text" name="title" class="span5 required" autofocus>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="list_grouping">List Grouping</label>
			<div class="controls">
				<input type="text" name="list_grouping" class="span5" data-provide="typeahead" data-source="{{ $list_groupings }}">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="order_by">Order By</label>
			<div class="controls">
				{{ Form::select('order_by', array('created_at'=>'Date Created', 'updated_at'=>'Date Updated', 'precedence'=>'Precedence'), 'created_at') }}
				{{ Form::select('direction', array('ASC'=>'Ascending', 'DESC'=>'Descending'), 'ASC') }}
			</div>
		</div>

		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Save changes</button>
			<a class="btn" href="{{ URL::to_route('objects') }}">Cancel</a>
		</div>		
	</form>
@endsection