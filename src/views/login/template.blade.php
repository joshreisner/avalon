<!DOCTYPE HTML>
<html>
	<head>
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
	<body class="login">
		@yield('main')
		{{ HTML::script('/packages/joshreisner/avalon/assets/js/main.min.js') }}
	</body>
</html>