$(function($) {

	//generic form validator
	$('form').validate({
		errorElement:"span",
		errorClass:"help-inline",
		onfocusout:false,
		onkeyup:true,
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

	$('form input[type=text]').first().focus();
	

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



	
	//page specific, set users control group to show or hide the objects checkboxes
	$("div.control-group.role button").click(function(){
		if ($(this).attr('value') == 3) {
			$("div.control-group.permissions").removeClass("hidden");
		} else {
			$("div.control-group.permissions").addClass("hidden");
		}
	});
	
});













