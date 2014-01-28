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
		
		{{ HTML::script('/packages/joshreisner/avalon/js/jquery-1.11.0.min.js') }}
		{{ HTML::script('/packages/joshreisner/avalon/js/jquery.validate.min.js') }}
		{{ HTML::script('/packages/joshreisner/avalon/js/jquery.tablednd.0.8.min.js') }}
		{{ HTML::script('/packages/joshreisner/avalon/js/jquery.ui.widget.js') }}
		{{ HTML::script('/packages/joshreisner/avalon/js/jquery-file-upload/jquery.fileupload.js') }}
		{{ HTML::script('/packages/joshreisner/avalon/js/bootstrap.js') }}
		{{ HTML::script('/packages/joshreisner/avalon/js/redactor.js') }}		
		{{ HTML::script('/packages/joshreisner/avalon/js/jscolor/jscolor.js') }}
		{{ HTML::script('/packages/joshreisner/avalon/js/main.js') }}
	</body>
</html>