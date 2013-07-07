//@codekit-prepend "jquery-1.10.2.min.js"
//@codekit-prepend "jquery.validate.min.js"
//@codekit-prepend "bootstrap.js"
//@codekit-prepend "jquery.tablednd.0.8.min.js"

$(function(){

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
			}).fail(function() { alert("error"); });
		}
	});
});