<!DOCTYPE HTML>
<html>
	<head>
		<title>@yield('title')</title>
		<meta charset="UTF-8">
		{{ HTML::style('/packages/joshreisner/avalon/css/global.css') }}
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="span12 header">
					<img src="/packages/joshreisner/avalon/img/banner.png" width="1170" height="92">
				</div>
			</div>
			<div class="row">
				<div class="span9 main">				
					@yield('main')
				</div>
				<div class="span3 side">
					<div class="inner">
						@yield('side')
					</div>
				</div>
			</div>
		</div>
	</body>
</html>