<!DOCTYPE HTML>
<html>
	<head>
		<title>Login</title>
		<meta charset="UTF-8">
		{{ Asset::container('avalon')->styles() }}
		{{ Asset::container('avalon')->scripts() }}
	</head>
	<body>
		<a href="https://github.com/joshreisner/avalon"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_white_ffffff.png" alt="Fork me on GitHub"></a>		

		{{ Form::open(URL::to_route('login'), 'POST', array('class'=>'form-horizontal')); }}
		
		<div class="modal">
			<div class="modal-header">
				<h3>Please log in</h3>
			</div>
		
			<div class="modal-body">
		
				@if (!$count)
				<div class="control-group">
					<label class="control-label" for="firstname">First Name</label>
			    	<div class="controls">
			    		<input type="text" name="firstname" class="required" autofocus>
			    	</div>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="lastname">Last Name</label>
			    	<div class="controls">
			    		<input type="text" name="lastname" class="required">
			    	</div>
				</div>
				@endif
				
				<div class="control-group">
					<label class="control-label" for="email">Email</label>
			    	<div class="controls">
			    		<input type="text" name="email" class="required email"@if ($count) autofocus@endif>
			    	</div>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="password">Password</label>
			    	<div class="controls">
			    		<input type="password" name="password" class="required">
			    	</div>
				</div>
						
		    </div>
		
		    <div class="modal-footer">
		    	<input type="submit" class="btn btn-primary" value="Go">
		    </div>
		
		</div>
		
		</form>
		
	</body>
</html>