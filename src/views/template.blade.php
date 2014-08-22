<!DOCTYPE HTML>
<html>
	<head>
		<base href="/packages/joshreisner/avalon/assets/jscolor">
		<title>@yield('title')</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		{{ HTML::style('/packages/joshreisner/avalon/assets/css/main.min.css') }}
		@if (Config::has('avalon::css'))
			@foreach (Config::get('avalon::css') as $stylesheet)
			{{ HTML::style($stylesheet) }}
			@endforeach
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
		{{ HTML::script('/packages/joshreisner/avalon/assets/js/main.min.js') }}
		@yield('script')
	</body>
</html>