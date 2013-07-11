//@codekit-prepend "jquery-1.10.2.min.js"
//@codekit-prepend "jquery.validate.min.js"
//@codekit-prepend "bootstrap.js"
//@codekit-prepend "jquery.tablednd.0.8.min.js"
//@codekit-prepend "redactor.js"

$(function() {

	//generic form validator
	$('form').validate({
		errorElement:"span",
		errorClass:"help-inline",
		onfocusout:false,
    	onkeyup: function(element) { },
		highlight: function(element, errorClass, validClass) {
			if (element.type === 'radio') {
				//this.findByName(element.name).parent("div").parent("div").addClass("error").removeClass(validClass);
			} else {
				$(element).closest("div.control-group").addClass("error");
			}
		},
		unhighlight: function(element, errorClass, validClass) {
			if (element.type === 'radio') {
				//this.findByName(element.name).parent("div").parent("div").removeClass("error").addClass("success");
			} else {
				$(element).closest("div.control-group").removeClass("error");
			}
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
	$('table').on('click', 'td.active a', function(e) {
		e.preventDefault();
		
		//toggle row class
		var parent = $(this).closest('tr');
		parent.toggleClass('inactive');
		if (parent.hasClass('inactive')) {
			var active = 0;
			$(this).find('i').removeClass('icon-check').addClass('icon-check-empty');
		} else {
			var active = 1;
			$(this).find('i').removeClass('icon-check-empty').addClass('icon-check');
		}
		
		//send ajax update
		$.get($(this).attr('href'), { active: active }, function(data){
			window.console.log('sent post and data was ' + data);
		}).fail(function() { 
			window.console.log('error');
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
			            editor.set(data.text);
						$('.redactor_act').removeClass('redactor_act');
			        });
                }
            }
        }
	});

	//slug fields
	$("input.slug").each(function() {
		$(this).on('keyup', function() {
			var val = $(this).val();
			val = val.toLowerCase().replace(/ /g, '-').replace(/\-\-/g, '-').replace(/[^a-z\-]/g, '');
			$(this).val(val);
		});	
	});

});