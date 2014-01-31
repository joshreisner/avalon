<!DOCTYPE HTML>
<html>
	<head>
		<title>@yield('title')</title>
		<meta charset="UTF-8">
		{{ HTML::style('/packages/joshreisner/avalon/css/main.css') }}
		@if (!empty($account->css))
		{{ HTML::style($account->css) }}
		@endif
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-md-12 header">
					<a href="{{ URL::action('ObjectController@index') }}"></a>
				</div>
			</div>
			<div class="row">
				<div class="col-md-9 main">				
					@yield('main')
				</div>
				<div class="col-md-3 side">
					<div class="inner">
						@yield('side')
					</div>
				</div>
			</div>
		</div>
		{{ HTML::script('/packages/joshreisner/avalon/js/jquery-1.11.0.min.js') }}
		{{ HTML::script('/packages/joshreisner/avalon/js/jquery.validate.min.js') }}
		{{ HTML::script('/packages/joshreisner/avalon/js/jquery-ui-1.10.4.custom.min.js') }}
		{{ HTML::script('/packages/joshreisner/avalon/js/jquery.mjs.nestedSortable.js') }}
		{{ HTML::script('/packages/joshreisner/avalon/js/jquery.tablednd.0.8.min.js') }}
		{{ HTML::script('/packages/joshreisner/avalon/js/jquery-file-upload/jquery.fileupload.js') }}
		{{ HTML::script('/packages/joshreisner/avalon/js/bootstrap.js') }}
		{{ HTML::script('/packages/joshreisner/avalon/js/bootstrap3-typeahead.min.js') }}
		{{ HTML::script('/packages/joshreisner/avalon/js/redactor.js') }}		
		{{ HTML::script('/packages/joshreisner/avalon/js/jscolor/jscolor.js') }}
		{{ HTML::script('/packages/joshreisner/avalon/js/main.js') }}
		
		@yield('script')

	</body>
</html>