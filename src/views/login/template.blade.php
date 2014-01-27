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
	<body class="login">
		<a href="https://github.com/joshreisner/avalon"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_white_ffffff.png" alt="Fork me on GitHub"></a>		

		@yield('main')
		
		{{ HTML::script('/packages/joshreisner/avalon/js/main.min.js') }}
	</body>
</html>