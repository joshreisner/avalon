@layout('avalon::page')

@section('breadcrumbs')
	<h1>
		<a href="/"><i class="icon-home"></i></a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		<a href="{{ URL::to_route('objects') }}">Objects</a>
		<span class="separator"><i class="icon-chevron-right"></i></span>
		Site Settings
	</h1>
@endsection

@section('main')
	<form class="form-horizontal">
		<div class="control-group">
			<label class="control-label" for="fieldname">Link Color</label>
			<div class="controls">
				<input type="text" name="link_color">
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
				<label class="checkbox inline">
					<input type="checkbox" id="inlineCheckbox1" value="option1"> Español
				</label>
				<label class="checkbox inline">
					<input type="checkbox" id="inlineCheckbox2" value="option2"> Français
				</label>
				<label class="checkbox inline">
					<input type="checkbox" id="inlineCheckbox3" value="option3"> Italiano
				</label>
				<label class="checkbox inline">
					<input type="checkbox" id="inlineCheckbox3" value="option3"> Portuguès
				</label>
				<label class="checkbox inline">
					<input type="checkbox" id="inlineCheckbox3" value="option3"> Русский
				</label>
				<label class="checkbox inline">
					<input type="checkbox" id="inlineCheckbox3" value="option3"> Українська
				</label>
			</div>
		</div>
		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Save changes</button>
			<button type="button" class="btn">Cancel</button>
		</div>

	</form>
@endsection