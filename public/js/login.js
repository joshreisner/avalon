$(function($) {
	$('form').validate({
		errorElement:"",
		errorClass:"help-inline",
		onfocusout:false,
		onkeyup:false,
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
});













