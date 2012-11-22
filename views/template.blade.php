<!DOCTYPE HTML>
<html>
	<head>
		<title>{{ $title }}</title>
		<meta charset="UTF-8">
		{{ Asset::container('avalon')->styles() }}
		{{ Asset::container('avalon')->scripts() }}
	</head>
	<body>
		<div class="container">
			<div class="row header">
				<div class="span12">
					<img src="/bundles/avalon/img/logo.jpg" alt="Your Logo Here" width="920" height="105">
				</div>
			</div>
			<div class="row">
				<div class="span9 main">
					@yield('breadcrumbs')
					@yield('buttons')
					@yield('main')
				</div>
				<div class="span3 side">
					@yield('side')
				</div>
			</div>
		</div>
	</body>
</html>
