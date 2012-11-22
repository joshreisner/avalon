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
	{{ Form::open(URL::to_route('settings'), 'PUT', array('class'=>'form-horizontal')) }}
		<div class="control-group">
			<label class="control-label" for="fieldname">Link Color</label>
			<div class="controls">
				<input type="text" name="link_color" class="color {hash:true}" value="{{ strtoupper($settings->link_color) }}">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="banner_image">Banner Image</label>
			<div class="controls">
				<input type="file" name="banner_image">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="fieldname">Languages</label>
			<div class="controls">
				@foreach ($languages as $key=>$value)
				<label class="checkbox inline">
					<input type="checkbox" name="languages_{{ $key }}"> {{ $value }}
				</label>
				@endforeach
			</div>
		</div>
		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Save changes</button>
			<a href="{{ URL::to_route('objects') }}" class="btn">Cancel</a>
		</div>

	</form>
@endsection