$(function($) {

	//global form functions

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
	

	//make all delete links use the DELETE HTTP verb
	$("td.delete a").append(function(){
        return "<form action='" + $(this).attr('href') + "' method='POST' class='hidden'><input type='hidden' name='_method' value='delete'></form>"
    }).removeAttr('href').attr('onclick','$(this).find("form").submit();');
    	

	//make all button groups live
	$("div.btn-group[data-toggle-name]").each(function(){
		var group   = $(this);
		var form    = group.parents('form').eq(0);
		var name    = group.attr('data-toggle-name');
		var hidden  = $('input[name="' + name + '"]', form);
		$('button', group).each(function(){
			var button = $(this);
			button.live('click', function(){
				hidden.val($(this).val());
			});
			if (button.val() == hidden.val()) button.addClass('active');
		});
	});


	//integer mask
    $("input.integer").keydown(function(event) {
    	// http://stackoverflow.com/questions/995183/how-to-allow-only-numeric-0-9-in-html-inputbox-using-jquery?page=1&tab=votes#tab-top
        // Allow: backspace, delete, tab, escape, and enter
        if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || 
             // Allow: Ctrl+A
            (event.keyCode == 65 && event.ctrlKey === true) || 
             // Allow: home, end, left, right
            (event.keyCode >= 35 && event.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        } else {
            // Ensure that it is a number and stop the keypress
            if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                event.preventDefault(); 
            }   
        }
    });


    //url mask
    $("input.url").keydown(function(event) {
		//alert(event.keyCode);
   		if ((event.keyCode >= 65) && (event.keyCode <= 90)) {
    		//a to z, return
    		return;
    	} else if ((event.keyCode == 8) || (event.keyCode == 9) || (event.keyCode == 13) || (event.keyCode == 16) || (event.keyCode == 20) || (event.keyCode == 91) || (event.keyCode == 189)) {
    		//del, tab, return, capslock, shift, command and underscore/dash ok
    		return;
    	} else if (event.shiftKey === true) {
    		if (event.keyCode == 186) {
    			//colon ok
    			return;
    		}
    	} else {
    		if ((event.keyCode == 190) || (event.keyCode == 191) || ((event.keyCode >= 48) && (event.keyCode <= 57))) {
    			//slash and numbers ok not sure what 190 is
    			return;
    		}
    	}
    	event.preventDefault();
    });


	//redactor
	$("textarea.textarea_rich").redactor();


	//make tables reorderable, requires some data- attributes
	$("table[data-reorder]").tableDnD({
	    onDragClass: "dragging",
	    dragHandle: "reorder",
	    onDrop: function(table, droppedRow, dragObjs) {

	    	var ids			= new Array();
	    	var precedences = new Array();
	        
	        $(table).find("tr[data-id]").each(function(){
	        	ids[ids.length]	= $(this).attr("data-id");
	        	precedences[precedences.length] = $(this).attr("data-precedence");
	        });

	        $.ajax({
	        	url: $(table).attr("data-reorder"),
	        	type: "POST",
	        	data: { ids: ids.join(","), precedences: precedences.join(",") },
	        	success: function(data) {
	        		//alert(data);
	        	}
	        });

	    }
	});

	//handle publish checkbox toggling
	$("table td.publish input").change(function(){
		var published = $(this).is(':checked');
        $.ajax({
        	url: $(this).attr('data-publish'),
        	type: "POST",
        	data: { published: published },
        	success: function(data) {
        		//alert(data);
        	}
        });
	});

	//local/page-specific functions

	//set users control group to show or hide the objects checkboxes
	$("form.user div.control-group.role button").click(function(){
		if ($(this).attr('value') == 3) {
			$("div.control-group.permissions").removeClass("hidden");
		} else {
			$("div.control-group.permissions").addClass("hidden");
		}
	});

	$("form.field select[name=type]").change(function(){
		if ($(this).val() == 'checkbox') {
			$("form.field div.control-group.required").slideUp();
		} else {
			$("form.field div.control-group.required").slideDown();			
		}
	});
	
});













