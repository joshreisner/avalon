//= include ../../../bower_components/jquery/dist/jquery.js
//= include ../../../bower_components/bootstrap-sass/dist/js/bootstrap.js
//= include ../../../bower_components/jquery-validate/dist/jquery.validate.js
//= include ../../../bower_components/moment/moment.js
//= include ../../../bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js
//= include ../../../bower_components/jquery-ui/jquery-ui.js
//= include ../../../bower_components/nestedSortable/jquery.ui.nestedSortable.js
//= include ../../../bower_components/TableDnD/js/jquery.tablednd.js
//= include ../../../bower_components/bootstrap3-typeahead/bootstrap3-typeahead.js
//= include ../../../bower_components/JSColor/jscolor.js
//= include ../../../bower_components/jquery-file-upload/js/jquery.fileupload.js
//= include ../redactor926/redactor/redactor.js

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

	//datetimepicker
	$('.input-group.date').datetimepicker({
        pickTime: false
    });

	$('.input-group.datetime').datetimepicker();

	//draggable tables
	$('table.draggable').tableDnD({
		dragHandle: '.draggy',
		onDragClass: 'dragging',
		onDrop: function(table, row) {
			$.post($(table).attr('data-draggable-url'), { order: $(table).tableDnDSerialize() }, function(data){
				//window.console.log('success with ' + data);
			}).fail(function() { 
				//window.console.log('error');
			});
		}
	});
	
	//nested sortable
	$('div.nested > ul').nestedSortable({
		listType: 'ul',
		forcePlaceholderSize: true,
		handle: 'div.draggy',
		helper: 'clone',
		items: 'li',
		opacity: 0.8,
		tabSize: 30,
		delay: 300,
		placeholder: 'placeholder',
		tolerance: 'pointer',
		toleranceElement: '> div',
		protectRoot: false,
		update: function(event, ui) {
			var id 				= ui.item.attr('id').substr(5);
			var arrayed 		= $('div.nested > ul').nestedSortable('toArray', {startDepthCount: 0});
			var list 			= new Array();
			var parent_id 		= false;

			for (var i = 0; i < arrayed.length; i++) {
				if (arrayed[i].item_id != 'root') list[list.length] = arrayed[i].item_id;
				if (arrayed[i].item_id == id) parent_id = arrayed[i].parent_id;
			}

			$.post($("div.nested").first().attr('data-draggable-url'), { 
					id : id,
					parent_id : parent_id, 
					list : list.join(',')
			}, function(data){
				//$('.side .inner').html(data);
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
			parent.find("td.updated_at").html(data);
		}).fail(function() { 
			//window.console.log('error');
		});
	});
	
	//toggle instance inside nested sortable
	$('div.nested').on('click', 'div.delete a', function(e) {
		e.preventDefault();
		
		//toggle row class
		var parent = $(this).closest('div.nested_row');
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
			parent.find("div.updated_at").html(data);
		}).fail(function() { 
			//window.console.log('error');
		});
	});
	
	/* redactor fields
	if (typeof RedactorPlugins === 'undefined') var RedactorPlugins = {};

	RedactorPlugins.advanced = {
	    init: function ()  {
	        this.buttonAdd('advanced', 'Lorem Ipsum', this.testButton);

	        // make your added button as Font Awesome's icon
	        //this.buttonAwesome('advanced', 'glyphicon glyphicon-ok-circle');
	    },
	    testButton: function(buttonName, buttonDOM, buttonObj, e)
	    {
	        alert(buttonName);
        	editor = this;
	        $.getJSON('http://hipsterjesus.com/api/', function(data) {
				$('.redactor_act').removeClass('redactor_act');
	            editor.set(data.text);
	            editor.focusEnd();
	        });
	    }
	};*/

	$('textarea.html').redactor({
		minHeight: 200,
		maxHeight: 500
		//, plugins: ['advanced']
	});


	//slug fields
	$("input.slug").each(function() {
		$(this).on('keyup', function() {
			var val = $(this).val();
			val = val.toLowerCase().replace(/ /g, '-').replace(/\-\-/g, '-').replace(/[^a-z0-9\-]/g, '');
			$(this).val(val);
		});	
	});

	//typeaheads	
	$("input.typeahead").each(function(){
		var $this = $(this);
		//console.log('hi');
		$.getJSON($this.attr("data-typeahead"), function(data){
		    $this.typeahead({ source:data });
			//console.log('there' + data);
		});
	});

	//single image upload
	$("div.image_upload").each(function(){
		var offset = $(this).offset();
		var width = $(this).width();
		var height = $(this).height();
		var field_id = $(this).attr('id').substr(6);
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
		dataType: 			"json", 
		acceptFileTypes : 	/(\.|\/)(jpg|gif|png)$/i,
		autoUpload: 		true,
		add: function(e, data) {
			data.submit();
		},
		fail: function(e, data) {
			//window.console.log('fail ' + data.jqXHR.responseText);
		},
		done: function(e, data) {
			//var file_id = data.jqXHR.responseText;
			var field_id = $(this).parent().find("input[name=field_id]").val();

			//set dimensions
			$(this).parent().width(data.result.width).height(data.result.height);


			//console.log(data);
			$("div#image_" + field_id)
				.css('backgroundImage', 'url(' + data.result.url + ')')
				.addClass("filled")
				.width(data.result.width)
				.height(data.result.height)
				.next()
				.val(data.result.file_id);
		}
	});

	/*multiple images upload
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
	*/
});