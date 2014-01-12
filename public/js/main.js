//@codekit-prepend "jquery-1.10.2.min.js"
//@codekit-prepend "jquery.validate.min.js"
//@codekit-prepend "jquery.tablednd.0.8.min.js"
//@codekit-prepend "jquery.ui.widget.js"
//@codekit-prepend "jquery-file-upload/jquery.fileupload.js"
//@codekit-prepend "bootstrap.js"
//@codekit-prepend "redactor.js"

$(function() {

	//generic form validator
	$('form').validate({
		errorElement:"span",
		errorClass:"help-inline",
		onfocusout:false,
    	onkeyup: function(element) { },
		highlight: function(element, errorClass, validClass) {
			$(element).closest("div.form-group").addClass("has-error");
		},
		unhighlight: function(element, errorClass, validClass) {
			$(element).closest("div.form-group").removeClass("has-error");
		}
	});

	//autoselect first text element that's not a color (they get messed up when they're autoselected)
	$('form input[type=text]:not(.color)').first().focus();

	//draggable tables
	$('table.draggable').tableDnD({
		dragHandle: '.draggy',
		onDragClass: 'dragging',
		onDrop: function(table, row) {
			$.post($(table).attr('data-draggable-url'), { order: $(table).tableDnDSerialize() }, function(data){
				//window.console.log('sent post and data was ' + data);
			}).fail(function() { 
				//window.console.log('error');
			});
		}
	});
	
	//toggle instance, field, or user active or inactive
	$('table').on('click', 'td.delete a', function(e) {
		e.preventDefault();
		
		//toggle row class
		var parent = $(this).closest('tr');
		parent.toggleClass('inactive');
		if (parent.hasClass('inactive')) {
			var active = 0;
			$(this).find('i').removeClass('glyphicon-ok-circle').addClass('glyphicon-remove-circle');
		} else {
			var active = 1;
			$(this).find('i').removeClass('glyphicon-remove-circle').addClass('glyphicon-ok-circle');
		}
		
		//send ajax update
		$.get($(this).attr('href'), { active: active }, function(data){
			//window.console.log('sent post and data was ' + data);
			parent.find("td.updated").html(data);
		}).fail(function() { 
			//window.console.log('error');
		});
	});
	
	//redactor fields
	$('textarea.html').redactor({
		minHeight: 200,
		buttonsAdd: ['|', 'button1'],
        buttonsCustom: {
            button1: {
                title: 'Lorem Ipsum',
                callback: function(buttonName, buttonDOM, buttonObject) {
                	editor = this;
			        $.getJSON('http://hipsterjesus.com/api/', function(data) {
						$('.redactor_act').removeClass('redactor_act');
			            editor.set(data.text);
			            editor.focusEnd();
			        });
                }
            }
        }
        //, s3: '/login/upload/file/to/s3'
	});

	//slug fields
	$("input.slug").each(function() {
		$(this).on('keyup', function() {
			var val = $(this).val();
			val = val.toLowerCase().replace(/ /g, '-').replace(/\-\-/g, '-').replace(/[^a-z0-9\-]/g, '');
			$(this).val(val);
		});	
	});

	function make_slug(str, len) {
		str = str.trim().toLowerCase();
		str = str.replace(/[^a-z0-9]+/g, '-');
		str = str.replace(/^-|-$/g, '');
		
		if (len && (str.length > len)) str = str.substr(0, len);
		
		if (str.substr(str.length - 1) == '-') str = str.substr(0, (str.length - 1));

		return str;
	}

	function random_string(len) {
		var str = '';
		var characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		for (var i = 0; i < len; i++) str += characters.charAt(Math.floor(Math.random() * characters.length));
		return str;
	}

	function file_name(str) {
		if (str.indexOf('.') == -1) return false;
		var fileparts = str.split('.');
		var extension = fileparts.pop();
		if (extension == 'jpeg') extension = 'jpg'; //hate jpeg
		var okfiletypes = new Array('jpg', 'png', 'gif');
		if ($.inArray(extension, okfiletypes) == -1) {
			alert('extension ' + extension + ' is invalid for this field.  Please use jpg, png or gif.');
			return false; //throw error?
		}
		var filename = 'images/' + make_slug(fileparts.join('.'), 20) + '-' + random_string(5) + '.' + extension;
		return filename;
	}

	//multiple images upload
	var well = $(".control-group.images .controls");
	var images = new Array();
	$("input#image_upload").fileupload({
		url: 				$(this).closest("form").attr('action'),
		type: 				'POST',
		dataType: 			"xml", 
		acceptFileTypes : 	/(\.|\/)(jpg|gif|png)$/i,
		autoUpload: 		true,
		dropZone: 			well,
		add: function(e, data) {
        	if (data.filename = file_name(data.files[0].name)) {
	        	data.context = $('<div class="image loading"/>').html('<div class="progress progress-striped active"><div class="bar"></div></div>').appendTo(well);
				$(this).closest("form").find("input[name=filename]").val(data.filename);
				data.submit();
        	}
		},
		fail: function(e, data) {
			window.console.log('fail');
			window.console.log(data);
		},
		progress: function(e, data){
			//update progress bar
			var percent = Math.round((e.loaded / e.total) * 100);
			$(data.context).find('.bar').css('width', percent + '%');
		},
		done: function(e, data) {
			window.console.log('done');
			//window.console.log(data);
			var img = document.createElement('img');
			img.src = 'https://s3.amazonaws.com/josh-reisner-dot-com/' + data.filename;
			$(data.context).removeClass("loading").html(img);
			images[images.length] = data.filename;
			$("input[name=images]").val(images.join("|"));
		}
	});

	//i believe these allow the browser to accept a file dropzone
	window.addEventListener("dragover", function(e){
		e = e || event;
		e.preventDefault();
	}, false);
	window.addEventListener("drop", function(e){
		e = e || event;
		e.preventDefault();
	}, false);

});

$(window).load(function(){
	
	//single image upload
	$("div.upload_image img").each(function(){
		var offset = $(this).offset();
		var width = $(this).width();
		var height = $(this).height();
		var field_id = $(this).parent().attr("data-field");
		$("<form class='upload upload_image'><input type='hidden' name='field_id' value='" + field_id + "'><input type='file' name='image'></form>").appendTo("body").css({
			top: offset.top, 
			left: offset.left,
			width: width,
			height: height,
			display: 'block'
		});
	});

	$("form.upload_image input").fileupload({
		url: 				"/login/upload/image",
		type: 				"POST",
		dataType: 			"text", 
		acceptFileTypes : 	/(\.|\/)(jpg|gif|png)$/i,
		autoUpload: 		true,
		add: function(e, data) {
			data.submit();
		},
		fail: function(e, data) {
			window.console.log('fail ' + data.jqXHR.responseText);
		},
		done: function(e, data) {
			window.console.log('done ' + data.jqXHR.responseText);
		}
	});
});