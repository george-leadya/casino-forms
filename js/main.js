jQuery(document).ready(function($) {
	$('#form_collector').on('submit', function(e){
	});
	
	$('#form_collector').validate({
		rules: {
			form_user_name: {
				required: true,
			},
			form_user_email: {
				required: true,
				email: true
			},
			form_user_phone: {
				required: true,
				phoneUS: true
			}
		},
		errorClass: "invalid",
		errorPlacement: function(error, element) {
			error.insertBefore( element );
		}
	});
	
	jQuery.validator.addMethod("phoneUS", function(phone_number, element){
		phone_number = phone_number.replace(/\s+/g, "");
		return this.optional(element) || phone_number.length > 9 && phone_number.match(/\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/);
	}, "Please specify a valid phone number");
	
	
	$('.tabs li.next span').on('click', function(e){
		$('#form_collector').submit();
	});
});