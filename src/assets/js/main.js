//= include ../../../bower_components/jquery/dist/jquery.js
//= include ../../../bower_components/bootstrap-sass/assets/javascripts/bootstrap.js
//= include ../../../bower_components/jquery-validate/dist/jquery.validate.js
//= include ../../../bower_components/moment/moment.js
//= include ../../../bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js
//= include ../../../bower_components/jquery-ui/jquery-ui.js
//= include ../../../bower_components/nestedSortable/jquery.ui.nestedSortable.js
//= include ../../../bower_components/TableDnD/js/jquery.tablednd.js
//= include ../../../bower_components/bootstrap3-typeahead/bootstrap3-typeahead.js
//= include ../../../bower_components/JSColor/jscolor.js
//= include ../../../bower_components/jquery-file-upload/js/jquery.fileupload.js
//= include ../redactor1009/redactor/redactor.js

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

	//autoselect first text element that's not a color (jscolor gets messed up when autoselected)
	$('form input[type=text]:not(.color,.slug)').first().focus();

	//datetimepickers
	$('.input-group.datetime').datetimepicker();
	$('.input-group.date').datetimepicker({pickTime: false});
	$('.input-group.time').datetimepicker({pickDate: false});

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
	
	//instance index search
	$('form#search').on('change', 'select', function(){
		$('form#search').submit();
	}).on('submit', function(){
        var text = $(this).find(":input").filter(function(){
            return $.trim(this.value).length > 0
        }).serialize();
        if (text.length) text = '?' + text;
        window.location.href = window.location.href.split('?')[0] + text;
        return false;
    }).on('click', 'i.glyphicon', function(){
	    $('form#search input').val('');
		$('form#search').submit();
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
		buttonSource: true,
		minHeight: 240,
		maxHeight: 500,
		removeAttr:  [
			['a', 'style'],
			['blockquote', 'style'],
			['em', 'style'],
			['hr', ['style', 'id']],
			//['p', 'style'],
			['span', 'style'],
			['strong', 'style']
    	],
    	initCallback: function() {
    		//console.log(this.code.get());
    	}
		//, plugins: ['advanced']
	});


	//slug fields -- todo improve
	$("input.slug").each(function() {
		$(this).on('keyup', function() {
			var val = $(this).val();
			val = val.toLowerCase().replace(/ /g, '-').replace(/\-\-/g, '-').replace(/[^a-z0-9\-]/g, '');
			if ($(this).val() != val) $(this).val(val);
		});	
	});

	//typeaheads	
	$("input.typeahead").each(function(){
		var $this = $(this);
		$.getJSON($this.attr("data-typeahead"), function(data){
		    $this.typeahead({ source:data });
		});
	});

	//handle remove event
	$("body").on("click", "form.upload a.remove", function(){
		var $form = $(this).parent("form");
		var $div = $("div[data-form-id=" + $form.attr("id") + "]");
		var $sibs = $div.siblings("div.image");
		var field_id = $div.attr("data-field-id");
		var $hidden = $div.closest(".form-group").find("input[type=hidden]");
		$form.remove();
		$div.remove(); //todo animate
		$hidden.setUploadedIds(field_id);
		$sibs.each(function(){
			$(this).checkUploadForm();
		});

	})

	//jquery function to cover a input element, used on page load and when cloning
	jQuery.fn.extend({
		setUploadedIds : function(field_id) {
			var ids = new Array();
			$(".image[data-field-id=" + field_id + "]:not(.new)").each(function(){
				ids[ids.length] = $(this).attr('data-file-id')
			});
			$(this).val(ids.join(","));
		},
		checkUploadForm : function() {
			var offset   = $(this).offset();
			var width    = $(this).width();
			var height   = $(this).height();
			$("form#" + $(this).attr("data-form-id")).css({
				top: offset.top, 
				left: offset.left,
				width: width,
				height: height
			});
		},
		setupUploadForm : function() {
			var random	 = randomStr();
			var offset   = $(this).offset();
			var width    = $(this).width();
			var height   = $(this).height();
			var field_id = $(this).attr("data-field-id");
			var multiple = $(this).closest(".form-group").hasClass("field-images");
			var isnew    = $(this).hasClass("new");

			//set form attr
			$(this).attr("data-form-id", random);

			//create form
			if (multiple) {				
				$('<form id="' + random + '" class="upload upload_image' + (isnew ? ' new' : '') + '">' + 
					'<input type="hidden" name="field_id" value="' + field_id + '">' + 
					'<input type="file" name="image" multiple>' +
					'<a class="remove"><i class="glyphicon glyphicon-remove-circle"></i></a>' +
					'</form>')
					.appendTo("body");
			} else {
				$('<form id="' + random + '" class="upload upload_image">' + 
					'<input type="hidden" name="field_id" value="' + field_id + '">' + 
					'<input type="file" name="image">' +
					'</form>')
					.appendTo("body");
			}

			//position form
			$(this).checkUploadForm();		
				
			//set upload event on form input
			$("form#" + random + " input[type=file]").fileupload({
				url: 				"/login/upload/image",
				type: 				"POST",
				dataType: 			"json", 
				acceptFileTypes : 	/(\.|\/)(jpg|gif|png)$/i,
				autoUpload: 		true,
				add: function(e, data) {
					data.submit();
				},
				fail: function(e, data) {
					//window.console.log(data.jqXHR.responseJSON.error);
					window.console.log(data.jqXHR.responseText);
				},
				done: function(e, data) {
					//window.console.log(data);

					//get some vars
					var multiple = $(this).prop("multiple");
					var $form = $(this).parent();
					var field_id = $form.find("input[name=field_id]").val();
					var $div = $("div.image[data-form-id=" + $form.attr("id") + "]");
					var $hidden = $div.closest(".form-group").find("input[type=hidden]");

					//if multiple, make sure to keep a new one around
					if (multiple && $div.hasClass("new")) {
						$div.clone().addClass("new").removeAttr("id").appendTo($div.parent()).setupUploadForm();
					}

					//adjust dimensions for the parent <form>
					$form.removeClass("new").width(data.result.screenwidth).height(data.result.screenheight);

					//set the image as background on the underlying <div> and resize
					$div.css('backgroundImage', 'url(' + data.result.url + ')')
						.removeClass("new")
						.attr("data-file-id", data.result.file_id)
						.css('lineHeight', data.result.screenheight + 'px')
						.width(data.result.screenwidth)
						.height(data.result.screenheight);

					//update hidden field value that will be passed with this form
					$hidden.setUploadedIds(field_id);
				}
			});

		}
	});

	//set up image upload <form>s on load
	$("div.form-group.field-image div.image").each(function(){
		$(this).setupUploadForm();
	});

	$("div.form-group.field-images div.image").each(function(){
		$(this).setupUploadForm();
	});

	function randomStr() {
		var m = 36, s = '', r = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		for (var i = 0; i < m; i++) { 
			s += r.charAt(Math.floor(Math.random()*r.length)); 
		}
		return s;
	};

});