<!DOCTYPE HTML>
<html>
	<head>
		<title>@yield('title')</title>
		<meta charset="UTF-8">
		{{ HTML::style('/packages/joshreisner/avalon/assets/css/main.min.css') }}
		@if (Config::has('avalon::css'))
			@foreach (Config::get('avalon::css') as $stylesheet)
			{{ HTML::style($stylesheet) }}
			@endforeach
		@endif
	</head>
	<body class="login">
		<a class="github" href="https://github.com/joshreisner/avalon" title="Fork me on GitHub"></a>		
		@yield('main')
		{{ HTML::script('/packages/joshreisner/avalon/assets/js/main.min.js') }}
	</body>
</html>