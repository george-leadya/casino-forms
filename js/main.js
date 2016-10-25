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
		},
		messages: {
			form_user_name: conf.err_required_field,
			form_user_email: {
				required: conf.err_required_field,
				email: conf.err_invalid_email
			},
			form_user_phone: conf.err_required_field
		}
	});
	
	jQuery.validator.addMethod("phoneUS", function(phone_number, element){
		phone_number = phone_number.replace(/\s+/g, "");
		return this.optional(element) || phone_number.match(/\(?([0-9]{3})\)?([ .-]?)\2([0-9]{3})/);
	}, conf.err_invalid_phone);
	
	$('.tabs li.next span').on('click', function(e){
		$('#form_collector').submit();
	});
	
	if( $('#lang_sel a.lang_sel_sel').length ){
		$('#lang_sel a.lang_sel_sel').on('click', function(e){
			$('body').toggleClass('showlang');
			e.preventDefault();
		});
	}
});