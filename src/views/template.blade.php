<!DOCTYPE HTML>
<html>
	<head>
		<title>@yield('title')</title>
		<meta charset="UTF-8">
		{{ HTML::style('/packages/joshreisner/avalon/css/main.css') }}
		<style type="text/css">
			a, a:hover, a:active { color: #{{ $account->color }}; }
			.btn-primary, .btn-primary:hover { background-color: #{{ $account->color }}; border-color: #{{ $account->color }}; }
		</style>
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-md-12 header">
					<a href="{{ URL::action('ObjectController@index') }}"><img src="{{ $account->image }}" width="1170" height="92"></a>
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
		{{ HTML::script('/packages/joshreisner/avalon/js/jscolor/jscolor.js') }}
		{{ HTML::script('/packages/joshreisner/avalon/js/main.min.js') }}
	</body>
</html>