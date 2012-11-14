<!DOCTYPE HTML>
<html>
	<head>
		<title>Login</title>
		<meta charset="UTF-8">
		{{ Asset::container('avalon')->styles() }}
		{{ Asset::container('avalon')->scripts() }}
		{{ Asset::styles() }}
		{{ Asset::scripts() }}
	</head>
	<body>
			<a href="https://github.com/joshreisner/avalon"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_white_ffffff.png" alt="Fork me on GitHub"></a>		
			@yield('content')
	</body>
</html>
